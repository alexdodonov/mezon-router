<?php
namespace Mezon\Router\Tests\Base;

use Mezon\Router\RouterInterface;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class RouterUnitTestClass extends BaseRouterUnitTestClass
{

    /**
     * Method creates router object
     *
     * @return RouterInterface
     */
    protected abstract function getRouter(): RouterInterface;

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
        RouterUnitTestUtils::setRequestMethod('GET');
    }

    /**
     * Function simply returns string.
     */
    public function helloWorldOutput(): string
    {
        return 'Hello world!';
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
            ]
        ];
    }

    /**
     * Testing router with different handlers
     *
     * @param string $url
     *            url
     * @param callable $handler
     *            handler
     * @param string $expectedResult
     *            expected result
     * @dataProvider differentHandlersDataProvider
     */
    public function testDifferentHandlers(string $url, callable $handler, string $expectedResult): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute($url, $handler);

        // test body
        $content = $router->callRoute($url);

        // assertions
        $this->assertEquals($expectedResult, $content);
    }

    /**
     * Testing case when all processors exist
     */
    public function testRequestMethodsConcurrency(): void
    {
        $route = '/catalog/';
        $router = $this->getRouter();
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
            return RouterUnitTestUtils::DELETE_REQUEST_METHOD;
        }, RouterUnitTestUtils::DELETE_REQUEST_METHOD);

        RouterUnitTestUtils::setRequestMethod('POST');
        $result = $router->callRoute($route);
        $this->assertEquals($result, 'POST');

        RouterUnitTestUtils::setRequestMethod('GET');
        $result = $router->callRoute($route);
        $this->assertEquals($result, 'GET');

        RouterUnitTestUtils::setRequestMethod('PUT');
        $result = $router->callRoute($route);
        $this->assertEquals($result, 'PUT');

        RouterUnitTestUtils::setRequestMethod(RouterUnitTestUtils::DELETE_REQUEST_METHOD);
        $result = $router->callRoute($route);
        $this->assertEquals($result, RouterUnitTestUtils::DELETE_REQUEST_METHOD);
    }

    /**
     * Method increments assertion count
     */
    protected function errorHandler(): void
    {
        $this->assertTrue(true);
    }

    /**
     * Test validate custom error handlers.
     */
    public function testSetErrorHandler(): void
    {
        // setup
        $router = $this->getRouter();
        $router->setNoProcessorFoundErrorHandler(function () {
            $this->errorHandler();
        });

        // test body and assertions
        RouterUnitTestUtils::setRequestMethod('POST');
        $router->callRoute('/unexisting/');
    }

    /**
     * Testing addGetMethod method
     */
    public function testAddGetRoute(): void
    {
        // setup
        $router = $this->getRouter();
        $router->addGetRoute('/route/', $this, 'helloWorldOutput');

        // test body
        $result = $router->callRoute('/route/');

        // assertions
        $this->assertEquals('Hello world!', $result);
    }

    /**
     * Testing addPostMethod method
     */
    public function testAddPostRoute(): void
    {
        // setup
        $router = $this->getRouter();
        $router->addPostRoute('/route/', $this, 'helloWorldOutput');
        $_SERVER['REQUEST_METHOD'] = 'POST';

        // test body
        $result = $router->callRoute('/route/');

        // assertions
        $this->assertEquals('Hello world!', $result);
    }
}
