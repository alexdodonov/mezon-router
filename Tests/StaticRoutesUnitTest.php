<?php

class StaticRoutesUnitTest extends \PHPUnit\Framework\TestCase
{

    const HELLO_WORLD = 'Hello world!';

    const HELLO_STATIC_WORLD = 'Hello static world!';

    /**
     * Function simply returns string.
     */
    public function helloWorldOutput(): string
    {
        return StaticRoutesUnitTest::HELLO_WORLD;
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
     * Testing one processor for all routes.
     */
    public function testSingleAllProcessor(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);

        $content = $router->callRoute('/some-star-compatible-route/');

        $this->assertEquals(StaticRoutesUnitTest::HELLO_WORLD, $content);
    }

    /**
     * Function simply returns string.
     */
    static public function staticHelloWorldOutput(): string
    {
        return StaticRoutesUnitTest::HELLO_STATIC_WORLD;
    }

    /**
     * Testing one component router.
     */
    public function testOneComponentRouterStatic(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('/one-component-static/', 'StaticRoutesUnitTest::staticHelloWorldOutput');

        $content = $router->callRoute('/one-component-static/');

        $this->assertEquals(StaticRoutesUnitTest::HELLO_STATIC_WORLD, $content);
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorOverlapUnexisting(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);
        $router->addRoute('/index/', 'StaticRoutesUnitTest::staticHelloWorldOutput');

        $content = $router->callRoute('/some-route/');

        $this->assertEquals(StaticRoutesUnitTest::HELLO_WORLD, $content);
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorOverlapExisting(): void
    {
        $router = new \Mezon\Router\Router();
        $router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);
        $router->addRoute('/index/', 'StaticRoutesUnitTest::staticHelloWorldOutput');

        $content = $router->callRoute('/index/');

        $this->assertEquals(StaticRoutesUnitTest::HELLO_WORLD, $content);
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorExisting(): void
    {
        // setup
        $router = new \Mezon\Router\Router();
        $router->addRoute('/index/', 'StaticRoutesUnitTest::staticHelloWorldOutput');
        $router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);

        // test body
        $content = $router->callRoute('/index/');

        // assertions
        $this->assertEquals(StaticRoutesUnitTest::HELLO_STATIC_WORLD, $content);
    }

    /**
     * Testing one processor for all routes overlap.
     */
    public function testSingleAllProcessorUnexisting(): void
    {
        // setup
        $router = new \Mezon\Router\Router();
        $router->addRoute('/index/', 'StaticRoutesUnitTest::staticHelloWorldOutput');
        $router->addRoute('*', [
            $this,
            'helloWorldOutput'
        ]);

        // test body
        $content = $router->callRoute('/some-route/');

        // assertions
        $this->assertEquals(StaticRoutesUnitTest::HELLO_WORLD, $content);
    }

    /**
     * Testing routeExists
     */
    public function testRouteExists(): void
    {
        // setup
        $router = new \Mezon\Router\Router();
        $router->addRoute('/searching-route/', function (string $route) {return $route;});

        // test body and assertions
        $this->assertTrue($router->routeExists('searching-route'));
        $this->assertFalse($router->routeExists('not-searching-route'));
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
        $_SERVER['REQUEST_URI'] = '/catalog/item/';

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
        $_SERVER['REQUEST_URI'] = '/';

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
        $_SERVER['REQUEST_URI'] = '/';

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
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route static-delete-unexisting";

        $this->assertNotFalse(strpos($exception, $msg));
    }

    /**
     * Testing static routes for DELETE requests.
     */
    public function testDeleteRequestForExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = RouterUnitTest::DELETE_REQUEST_METHOD;

        $router = new \Mezon\Router\Router();
        $router->addRoute('/static-delete-existing/', function ($route) {
            return $route;
        }, RouterUnitTest::DELETE_REQUEST_METHOD);

        $result = $router->callRoute('/static-delete-existing/');

        $this->assertEquals($result, 'static-delete-existing');
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
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route static-put-unexisting";

        $this->assertNotFalse(strpos($exception, $msg));
    }

    /**
     * Testing static routes for PUT requests.
     */
    public function testPutRequestForExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';

        $router = new \Mezon\Router\Router();
        $router->addRoute('/static-put-existing/', function ($route) {
            return $route;
        }, 'PUT');

        $result = $router->callRoute('/static-put-existing/');

        $this->assertEquals($result, 'static-put-existing');
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
        } catch (Exception $e) {
            $exception = $e->getMessage();
        }

        $msg = "The processor was not found for the route static-post-unexisting";

        $this->assertNotFalse(strpos($exception, $msg));
    }

    /**
     * Testing static routes for POST requests.
     */
    public function testPostRequestForExistingStaticRoute(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $router = new \Mezon\Router\Router();
        $router->addRoute('/catalog/', function ($route) {
            return $route;
        }, 'POST');

        $result = $router->callRoute('/catalog/');

        $this->assertEquals($result, 'catalog');
    }
}
