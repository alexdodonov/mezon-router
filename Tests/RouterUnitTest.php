<?php
namespace Mezon\Router\Tests;

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
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route";

        $this->assertNotFalse(strpos($exception, $msg), 'Valid error handling expected');
    }

    /**
     * Data provider for the test testClassAction
     *
     * @return array test data
     */
    public function classActionDataProvider(): array
    {
        $testData = [];

        foreach ([
            'GET',
            'POST'
        ] as $requestMethod) {
            $testData[] = [
                $requestMethod,
                '/a1/',
                'action #1'
            ];

            $testData[] = [
                $requestMethod,
                '/a2/',
                'action #2'
            ];

            $testData[] = [
                $requestMethod,
                '/double-word/',
                'action double word'
            ];
        }

        return $testData;
    }

    /**
     * Method tests class actions
     *
     * @param string $requestMethod
     *            request method
     * @param string $route
     *            requesting route
     * @param string $expectedResult
     *            expected result of route processing
     * @dataProvider classActionDataProvider
     */
    public function testClassAction(string $requestMethod, string $route, string $expectedResult): void
    {
        RouterUnitTest::setRequestMethod($requestMethod);

        $router = new \Mezon\Router\Router();
        $router->fetchActions($this);
        $result = $router->callRoute($route);
        $this->assertEquals($expectedResult, $result);
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
        // TODO user getListOfSupportedRequestMethods() to fetch supported request methods
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
        } catch (\Exception $e) {
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
        // setup
        $router = new \Mezon\Router\Router();
        $router->setNoProcessorFoundErrorHandler(function () {
            $this->errorHandler();
        });

        // test body and assertions
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

    /**
     * Data provider for the test testReverseRouteByName
     *
     * @return array test data
     */
    public function reverseRouteByNameDataProvider(): array
    {
        return [
            [
                'named-route-url',
                [],
                'named-route-url'
            ],
            [
                'route-with-params/[i:id]',
                [
                    'id' => 123
                ],
                'route-with-params/123',
            ],
            [
                'route-with-foo/[i:id]',
                [
                    'foo' => 123
                ],
                'route-with-foo/[i:id]',
            ],
            [
                'route-no-params/[i:id]',
                [],
                'route-no-params/[i:id]',
            ]
        ];
    }

    /**
     * Testing reverse route by it's name
     *
     * @param string $route
     *            route
     * @param array $parameters
     *            parameters to be substituted
     * @param string $extectedResult
     *            quite obviuous to describe it here )
     * @dataProvider reverseRouteByNameDataProvider
     */
    public function testReverseRouteByName(string $route, array $parameters = [], string $extectedResult): void
    {
        // setup
        $router = new \Mezon\Router\Router();
        $router->addRoute($route, function () {
            return 'named route result';
        }, 'GET', 'named-route');

        // test body
        $url = $router->reverse('named-route', $parameters);

        // assertions
        $this->assertEquals($extectedResult, $url);
    }

    /**
     * Trying to fetch unexisting route by name
     */
    public function testFetchUnexistingRouteByName(): void
    {
        // setup
        $router = new \Mezon\Router\Router();

        // assertions
        $this->expectException(\Exception::class);

        // test body
        $router->getRouteByName('unexisting-name');
    }
}
