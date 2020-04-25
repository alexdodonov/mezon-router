<?php

class RouterUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Default setup
     *
     * {@inheritdoc}
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    public function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
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
        $router = new \Mezon\Router\Router();

        $router->addRoute('/index/', [
            $this,
            'helloWorldOutput'
        ]);

        $content = $router->callRoute('/index/');

        $this->assertEquals('Hello world!', $content);
    }

    /**
     * Testing one component router.
     */
    public function testOneComponentRouterLambda(): void
    {
        $router = new \Mezon\Router\Router();

        $router->addRoute('/index/', function () {
            return 'Hello world!';
        });

        $content = $router->callRoute('/index/');

        $this->assertEquals('Hello world!', $content);
    }

    /**
     * Testing unexisting route behaviour.
     */
    public function testUnexistingRoute(): void
    {
        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/index/', [
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
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $router = new \Mezon\Router\Router();
        $router->fetchActions($this);
        $content = $router->callRoute('/a1/');
        $this->assertEquals('action #1', $content, 'Invalid a1 route');
    }

    /**
     * Testing case when both GET and POST processors exists.
     */
    public function testGetPostPostDeleteRouteConcurrency(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/', function () {
            return 'POST';
        }, 'POST');
        $router->addRoute('/catalog/', function () {
            return 'GET';
        }, 'GET');
        $router->addRoute('/catalog/', function () {
            return 'PUT';
        }, 'PUT');
        $router->addRoute('/catalog/', function () {
            return 'DELETE';
        }, 'DELETE');

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $result = $router->callRoute('/catalog/');
        $this->assertEquals($result, 'POST');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $result = $router->callRoute('/catalog/');
        $this->assertEquals($result, 'GET');

        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $result = $router->callRoute('/catalog/');
        $this->assertEquals($result, 'PUT');

        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $result = $router->callRoute('/catalog/');
        $this->assertEquals($result, 'DELETE');
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
                'DELETE'
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
        $router->addRoute('/catalog/', function () use ($method) {
            return $method;
        }, $method);
        $router->clear();

        try {
            $_SERVER['REQUEST_METHOD'] = $method;
            $router->callRoute('/catalog/');
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

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $router->callRoute('/unexisting/');
    }
}
