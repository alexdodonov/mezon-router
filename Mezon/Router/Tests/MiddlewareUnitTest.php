<?php
namespace Mezon\Router\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Router\Router;

class MiddlewareUnitTest extends TestCase
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
     * Testing middleware
     */
    public function testMiddleware(): void
    {
        // setup
        $route = '/route-with-middleware/';
        $router = new Router();
        $router->addRoute($route, function (int $i, int $j) {
            return [
                $i,
                $j
            ];
        });
        $router->registerMiddleware($route, function () {
            return [
                1,
                2
            ];
        });

        // test body
        $result = $router->callRoute($route);

        // assertions
        $this->assertEquals(1, $result[0]);
        $this->assertEquals(2, $result[1]);
    }
}
