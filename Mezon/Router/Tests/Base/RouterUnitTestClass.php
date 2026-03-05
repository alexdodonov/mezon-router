<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

use Mezon\Router\RouterInterface;
use PHPUnit\Framework\TestCase;

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
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function setRequestMethod(string $requestMethod): void
    {
        $_SERVER['REQUEST_METHOD'] = $requestMethod;
    }

    /**
     * Default setup
     *
     * {@inheritdoc}
     * @see TestCase::setUp()
     */
    public function setUp(): void
    {
        RouterUnitTestUtils::setRequestMethod('GET');
    }

    /**
     * Data provider for the test.
     *
     * @return array test data.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function differentHandlersDataProvider(): array
    {
        return [
            # 0, class method
            [
                '/one-component-class-method/',
                [
                    new HelloWorld(),
                    'helloWorld'
                ],
                'Hello World!!!'
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
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testDifferentHandlers(string $url, callable $handler, string $expectedResult): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute($url, $handler);

        // test body
        /** @var string $content */
        $content = $router->callRoute($url);

        // assertions
        $this->assertEquals($expectedResult, $content);
    }

    /**
     * Testing case when all processors exist
     * 
     * @psalm-suppress PossiblyUnusedMethod
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
        /** @var string $result */
        $result = $router->callRoute($route);
        $this->assertEquals($result, 'POST');

        RouterUnitTestUtils::setRequestMethod('GET');
        /** @var string $result */
        $result = $router->callRoute($route);
        $this->assertEquals($result, 'GET');

        RouterUnitTestUtils::setRequestMethod('PUT');
        /** @var string $result */
        $result = $router->callRoute($route);
        $this->assertEquals($result, 'PUT');

        RouterUnitTestUtils::setRequestMethod(RouterUnitTestUtils::DELETE_REQUEST_METHOD);
        /** @var string $result */
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
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testSetErrorHandler(): void
    {
        // setup
        $router = $this->getRouter();
        $router->setNoProcessorFoundErrorHandler(function (): void {
            $this->errorHandler();
        });

        // test body and assertions
        RouterUnitTestUtils::setRequestMethod('POST');
        $router->callRoute('/unexisting/');
    }
}
