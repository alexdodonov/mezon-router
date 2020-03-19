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
        $router->addRoute('/index/', 'StaticRoutesUnitTest::staticHelloWorldOutput');

        $content = $router->callRoute('/index/');

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
    public function testRouteExists():void{
        // setup
        $router = new \Mezon\Router\Router();
        $router->addRoute('/searching-route/', function(){});

        // test body and assertions
        $this->assertTrue($router->routeExists('/searching-route/'));
        $this->assertFalse($router->routeExists('not-searching-route'));
    }
}
