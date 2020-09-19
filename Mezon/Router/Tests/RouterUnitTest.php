<?php
namespace Mezon\Router\Tests;

use Mezon\Router\Router;

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
     * Data provider for the test
     *
     * @return array
     */
    public function differentHandlersDataProvider(): array
    {
        return [
            # 0, class method
            [
                '/one-component-class-method/',
                [
                    $this,
                    'helloWorldOutput'
                ],
                'Hello world!'
            ],
            # 1, lambda
            [
                '/one-component-lambda/',
                function () {
                    return 'Hello lambda!';
                },
                'Hello lambda!'
            ],
        ];
    }

    /**
     * Testing router with different handlers
     *
     * @dataProvider differentHandlersDataProvider
     */
    public function testDifferentHandlers(string $url, $handler, string $expectedResult): void
    {
        // setup
        $router = new Router();
        $router->addRoute($url, $handler);

        // test body
        $content = $router->callRoute($url);

        // assertions
        $this->assertEquals($expectedResult, $content);
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
}
