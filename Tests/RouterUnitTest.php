<?php

class RouterUnitTest extends \PHPUnit\Framework\TestCase
{

    const DELETE_REQUEST_METHOD = 'DELETE';

    /**
     * Function sets $_SERVER['REQUEST_METHOD']
     *
     * @param string $requestMethod
     *            request method
     */
    public static function setRequestMethod(string $requestMethod): void
    {
        $_SERVER['REQUEST_METHOD'] = $requestMethod;
    }

    /**
     * Default setup
     *
     * {@inheritdoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    public function setUp(): void
    {
        RouterUnitTest::setRequestMethod('GET');
    }

    /**
     * Function simply returns string.
     */
    public function helloWorldOutput(): string
    {
        return 'Hello world!';
    }

    /**
     * Testing action #1.
     */
    public function actionA1(): string
    {
        return 'action #1';
    }

    /**
     * Testing action #2.
     */
    public function actionA2(): string
    {
        return 'action #2';
    }

    public function actionDoubleWord(): string
    {
        return 'action double word';
    }

    /**
     * Testing one component router.
     */
    public function testOneComponentRouterClassMethod(): void
    {
        // TODO join this test with the next one via data provider
        $router = new \Mezon\Router\Router();

        $router->addRoute('/one-component-class-method/', [
            $this,
            'helloWorldOutput'
        ]);

        $content = $router->callRoute('/one-component-class-method/');

        $this->assertEquals('Hello world!', $content);
    }

    /**
     * Testing one component router.
     */
    public function testOneComponentRouterLambda(): void
    {
        $router = new \Mezon\Router\Router();

        $router->addRoute('/one-comonent-lambda/', function () {
            return 'Hello world!';
        });

        $content = $router->callRoute('/one-comonent-lambda/');

        $this->assertEquals('Hello world!', $content);
    }

    /**
     * Testing unexisting route behaviour.
     */
    public function testUnexistingRoute(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/existing-route/', [
            $this,
            'helloWorldOutput'
        ]);

        try {
            $router->callRoute('/unexisting-route/');
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route";

        $this->assertNotFalse(strpos($exception, $msg), 'Valid error handling expected');
    }

    /**
     * Testing action fetching method.
     */
    public function testClassActions(): void
    {
        // TODO join this test with the next one via data provider
        $router = new \Mezon\Router\Router();
        $router->fetchActions($this);

        $content = $router->callRoute('/a1/');
        $this->assertEquals('action #1', $content, 'Invalid a1 route');

        $content = $router->callRoute('/a2/');
        $this->assertEquals('action #2', $content, 'Invalid a2 route');

        $content = $router->callRoute('/double-word/');
        $this->assertEquals('action double word', $content, 'Invalid a2 route');
    }

    /**
     * Method tests POST actions
     */
    public function testPostClassAction(): void
    {
        RouterUnitTest::setRequestMethod('POST');

        $router = new \Mezon\Router\Router();
        $router->fetchActions($this);
        $content = $router->callRoute('/a1/');
        $this->assertEquals('action #1', $content, 'Invalid a1 route');
    }

    /**
     * Testing case when all processors exist
     */
    public function testRequestMethodsConcurrency(): void
    {
        $route = '/catalog/';
        $router = new \Mezon\Router\Router();
        $router->addRoute($route, function () {
            return 'POST';
        }, 'POST');
        $router->addRoute($route, function () {
            return 'GET';
        }, 'GET');
        $router->addRoute($route, function () {
            return 'PUT';
        }, 'PUT');
        $router->addRoute($route, function () {
            return RouterUnitTest::DELETE_REQUEST_METHOD;
        }, RouterUnitTest::DELETE_REQUEST_METHOD);

        RouterUnitTest::setRequestMethod('POST');
        $result = $router->callRoute($route);
        $this->assertEquals($result, 'POST');

        RouterUnitTest::setRequestMethod('GET');
        $result = $router->callRoute($route);
        $this->assertEquals($result, 'GET');

        RouterUnitTest::setRequestMethod('PUT');
        $result = $router->callRoute($route);
        $this->assertEquals($result, 'PUT');

        RouterUnitTest::setRequestMethod(RouterUnitTest::DELETE_REQUEST_METHOD);
        $result = $router->callRoute($route);
        $this->assertEquals($result, RouterUnitTest::DELETE_REQUEST_METHOD);
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function clearMethodTestDataProvider(): array
    {
        return [
            [
                'POST'
            ],
            [
                'GET'
            ],
            [
                'PUT'
            ],
            [
                RouterUnitTest::DELETE_REQUEST_METHOD
            ],
            [
                'OPTION'
            ]
        ];
    }

    /**
     * Testing 'clear' method
     *
     * @param string $method
     *            request method
     * @dataProvider clearMethodTestDataProvider
     */
    public function testClearMethod(string $method): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/route-to-clear/', function () use ($method) {
            return $method;
        }, $method);
        $router->clear();

        try {
            RouterUnitTest::setRequestMethod($method);
            $router->callRoute('/route-to-clear/');
            $flag = 'not cleared';
        } catch (Exception $e) {
            $flag = 'cleared';
        }
        $this->assertEquals($flag, 'cleared', 'Data was not cleared');
    }

    /**
     * Method increments assertion count
     */
    protected function errorHandler(): void
    {
        $this->addToAssertionCount(1);
    }

    /**
     * Test validate custom error handlers.
     */
    public function testSetErrorHandler(): void
    {
        $router = new \Mezon\Router\Router();
        $router->setNoProcessorFoundErrorHandler(function () {
            $this->errorHandler();
        });

        RouterUnitTest::setRequestMethod('POST');
        $router->callRoute('/unexisting/');
    }

    /**
     * Data provider for the
     *
     * @return array testing dataset
     */
    public function getCallbackDataProvider(): array
    {
        return [
            [
                'some-static-route',
                'some-static-route'
            ],
            [
                'some/non-static/route/[i:id]',
                'some/non-static/route/1'
            ]
        ];
    }

    /**
     * Testing getting callback
     *
     * @param string $route
     *            route
     * @param string $url
     *            concrete URL
     * @dataProvider getCallbackDataProvider
     */
    public function testGetCallback(string $route, string $url): void
    {
        // setup
        $router = new \Mezon\Router\Router();
        $router->addRoute($route, function () {
            return 'route result';
        });

        // test body
        $callback = $router->getCallback($url);

        // assertions
        $this->assertEquals('route result', $callback());
    }

    /**
     * Testing case with unexisting callback
     */
    public function testGetCallbackWithUnexistingRoute(): void
    {
        // setup
        $router = new \Mezon\Router\Router();
        $router->addRoute('existing-route', function () {
            return 'existing route result';
        });

        // assertions
        $this->expectException(\Exception::class);

        // test body
        $router->getCallback('unexisting-route');
    }
}
