<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

use Mezon\Router\Tests\Utils;
use PHPUnit\Framework\TestCase;
use Mezon\Router\SupportedRequestMethods;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class StaticRoutesTestClass extends BaseRouterUnitTestClass
{

    use Utils;

    /**
     * Default setup
     *
     * {@inheritdoc}
     * @see TestCase::setUp()
     */
    public function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    /**
     * Testing exception throwing if the method was not found.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testUnknownMethodException(): void
    {
        // setup
        $_GET['r'] = 'unexisting-route-method';
        $router = $this->getRouter();
        $router->addRoute('/unexisting-route-method/', [
            $this,
            'unexistingMethod'
        ]);

        // assertions
        $this->expectException(\Exception::class);

        // test body
        $router->callRoute('/unexisting-route-method/');
    }

    /**
     * Method returns some testing string.
     *
     * @return string Some string.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function subArray(): string
    {
        return 'subArrayResult';
    }

    /**
     * Testing array routes.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testArrayRoutes(): void
    {
        $router = $this->getRouter();
        $router->addRoute('/part1/part2/', function (string $route): string {
            return $route;
        }, 'GET');

        /** @var string $result */
        $result = $router->callRoute([
            'part1',
            'part2'
        ]);

        $this->assertEquals($result, 'part1/part2');
    }

    /**
     * Testing empty array routes.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testEmptyArrayRoutes(): void
    {
        $this->setRequestUri('/catalog/item/');

        $router = $this->getRouter();
        $router->addRoute('/catalog/item/', function (string $route): string {
            return $route;
        }, 'GET');

        /** @var string $result */
        $result = $router->callRoute([
            0 => ''
        ]);

        $this->assertEquals($result, 'catalog/item');
    }

    /**
     * Testing empty array routes.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testIndexRoute(): void
    {
        $this->setRequestUri('/');

        $router = $this->getRouter();
        $router->addRoute('/index/', function (string $route): string {
            return $route;
        }, 'GET');

        /** @var string $result */
        $result = $router->callRoute([
            0 => ''
        ]);

        $this->assertEquals($result, 'index');
    }

    /**
     * Testing empty array routes.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testMultipleRequestTypes(): void
    {
        // setup
        $this->setRequestUri('/');

        $router = $this->getRouter();
        $router->addRoute('/index/', function (string $route): string {
            return $route;
        }, [
            'GET',
            'POST'
        ]);

        $router->callRoute([
            0 => ''
        ]);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        /** @var string $result */
        $result = $router->callRoute([
            0 => ''
        ]);

        $this->assertEquals($result, 'index');
    }

    /**
     * Testing static routes for DELETE requests.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testDeleteRequestForUnExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = RouterUnitTestUtils::DELETE_REQUEST_METHOD;

        $exception = '';
        $router = $this->getRouter();
        $router->addRoute('/static-delete-unexisting/', [
            new HelloWorld(),
            'helloWorld'
        ]);

        try {
            $router->callRoute('/static-delete-unexisting/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route static-delete-unexisting";

        $this->assertNotFalse(strpos($exception, $msg));
    }

    /**
     * Testing static routes for PUT requests.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testPutRequestForUnExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $exception = '';
        $router = $this->getRouter();
        $router->addRoute('/static-put-unexisting/', [
            new HelloWorld(),
            'helloWorld'
        ]);

        try {
            $router->callRoute('/static-put-unexisting/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route static-put-unexisting";

        $this->assertNotFalse(strpos($exception, $msg));
    }

    /**
     * Testing static routes for POST requests.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testPostRequestForUnExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $exception = '';
        $router = $this->getRouter();
        $router->addRoute('/static-post-unexisting/', [
            new HelloWorld(),
            'helloWorld'
        ]);

        try {
            $router->callRoute('/static-post-unexisting/');
        } catch (\Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route static-post-unexisting";

        $this->assertNotFalse(strpos($exception, $msg));
    }

    /**
     * Data provider.
     *
     * @return array test data.
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function clearMethodTestDataProvider(): array
    {
        $result = [];

        foreach (SupportedRequestMethods::getListOfSupportedRequestMethods() as $method) {
            $result[] = [
                $method
            ];
        }

        return $result;
    }

    /**
     * Testing 'clear' method
     *
     * @param string $method
     *            request method
     * @dataProvider clearMethodTestDataProvider
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testClearMethod(string $method): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute('/route-to-clear/', function () use ($method) {
            return $method;
        }, $method);

        // test body
        $router->clear();

        // assertions
        $this->expectException(\Exception::class);
        $router->callRoute('/route-to-clear/');
    }

    /**
     * Testing static routes calls for all possible request methods
     *
     * @param string $method
     * @dataProvider clearMethodTestDataProvider
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testRequestForExistingStaticRoute(string $method): void
    {
        $_SERVER['REQUEST_METHOD'] = $method;

        $router = $this->getRouter();
        $router->addRoute('/catalog/', function (string $route): string {
            return $route;
        }, $method);

        /** @var string $result */
        $result = $router->callRoute('/catalog/');

        $this->assertEquals($result, 'catalog');
    }

    /**
     * Testing routeExists
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testRouteExists(): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute('/searching-static-route/', function (string $route) {
            return $route;
        });
        $router->addRoute('/searching-param-route/[i:id]/', function (string $route) {
            return $route;
        });

        // test body and assertions
        $this->assertTrue($router->routeExists('searching-static-route'));
        $this->assertTrue($router->routeExists('searching-param-route/[i:id]'));
        $this->assertFalse($router->routeExists('not-searching-route'));
    }

    /**
     * Testing invalid request method
     * 
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function testInvalidRequestMethodException(): void
    {
        // assertions
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(- 1);
        $this->expectExceptionMessage('Unsupported request method: "INVALID"');

        // setup
        $router = $this->getRouter();
        $router->addRoute('/searching-static-route/', function (string $route) {
            return $route;
        });
        $_SERVER['REQUEST_METHOD'] = 'INVALID';

        // test body
        $router->callRoute('/searching-static-route/');
    }
}
