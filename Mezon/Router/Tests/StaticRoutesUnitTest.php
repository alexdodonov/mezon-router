<?php
namespace Mezon\Router\Tests;

class StaticRoutesUnitTest extends \PHPUnit\Framework\TestCase
{

    const HELLO_WORLD = 'Hello world!';

    const HELLO_STATIC_WORLD = 'Hello static world!';

    const HELLO_STATIC_WORLD_METHOD = '\Mezon\Router\Tests\StaticRoutesUnitTest::staticHelloWorldOutput';

    use Utils;

    /**
     * Function simply returns string.
     */
    public function helloWorldOutput(): string
    {
        return StaticRoutesUnitTest::HELLO_WORLD;
    }

    /**
     * Function simply returns string.
     */
    static public function staticHelloWorldOutput(): string
    {
        return StaticRoutesUnitTest::HELLO_STATIC_WORLD;
    }

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
     * Testing exception throwing if the method was not found
     */
    public function testUnknownMethodException(): void
    {
        // setup
        $_GET['r'] = 'unexisting-route-method';
        $router = new \Mezon\Router\Router();
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
     * Method returns some testing string
     *
     * @return string
     */
    public function subArray(): string
    {
        return 'subArrayResult';
    }

    /**
     * Testing completely not callable trash
     */
    public function testNotCallableTrash(): void
    {
        // setup
        $_GET['r'] = 'trash';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/trash/', []);

        // assertions
        $this->expectException(\Exception::class);

        // test body
        $router->callRoute('/trash/');
    }

    /**
     * Testing array routes
     */
    public function testArrayRoutes(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/part1/part2/', function ($route) {
            return $route;
        }, 'GET');

        $result = $router->callRoute([
            'part1',
            'part2'
        ]);

        $this->assertEquals($result, 'part1/part2');
    }

    /**
     * Testing empty array routes
     */
    public function testEmptyArrayRoutes(): void
    {
        $this->setRequestUri('/catalog/item/');

        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/item/', function ($route) {
            return $route;
        }, 'GET');

        $result = $router->callRoute([
            0 => ''
        ]);

        $this->assertEquals($result, 'catalog/item');
    }

    /**
     * Testing empty array routes
     */
    public function testIndexRoute(): void
    {
        $this->setRequestUri('/');

        $router = new \Mezon\Router\Router();
        $router->addRoute('/index/', function ($route) {
            return $route;
        }, 'GET');

        $result = $router->callRoute([
            0 => ''
        ]);

        $this->assertEquals($result, 'index');
    }

    /**
     * Testing empty array routes
     */
    public function testMultipleRequestTypes(): void
    {
        // setup
        $this->setRequestUri('/');

        $router = new \Mezon\Router\Router();
        $router->addRoute('/index/', function ($route) {
            return $route;
        }, [
            'GET',
            'POST'
        ]);

        $router->callRoute([
            0 => ''
        ]);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $result = $router->callRoute([
            0 => ''
        ]);

        $this->assertEquals($result, 'index');
    }

    /**
     * Testing static routes for DELETE requests.
     */
    public function testDeleteRequestForUnExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = RouterUnitTest::DELETE_REQUEST_METHOD;

        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/static-delete-unexisting/', [
            $this,
            'helloWorldOutput'
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
     */
    public function testPutRequestForUnExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/static-put-unexisting/', [
            $this,
            'helloWorldOutput'
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
     */
    public function testPostRequestForUnExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $exception = '';
        $router = new \Mezon\Router\Router();
        $router->addRoute('/static-post-unexisting/', [
            $this,
            'helloWorldOutput'
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
     * Data provider
     *
     * @return array
     */
    public function clearMethodTestDataProvider(): array
    {
        $result = [];

        foreach (\Mezon\Router\Router::getListOfSupportedRequestMethods() as $method) {
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
     * Testing static routes calls for all possible request methods
     *
     * @param string $method
     * @dataProvider clearMethodTestDataProvider
     */
    public function testRequestForExistingStaticRoute(string $method): void
    {
        $_SERVER['REQUEST_METHOD'] = $method;

        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/', function ($route) {
            return $route;
        }, $method);

        $result = $router->callRoute('/catalog/');

        $this->assertEquals($result, 'catalog');
    }

    /**
     * Testing routeExists
     */
    public function testRouteExists(): void
    {
        // setup
        $router = new \Mezon\Router\Router();
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
}
