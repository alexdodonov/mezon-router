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
        $router->registerMiddleware('*', function () {
            return [
                1,
                2
            ];
        });
        $router->addRoute($route, function (int $i, int $j) {
            return [
                $i,
                $j
            ];
        });
        $router->registerMiddleware($route, function (int $i, int $j) {
            return [
                $i,
                $j
            ];
        });

        // test body
        $result = $router->callRoute($route);

        // assertions
        $this->assertEquals(1, $result[0]);
        $this->assertEquals(2, $result[1]);
    }

    /**
     * Testing multiple middlewares
     */
    public function testMultipleMiddlewaresInOrderAndOmitMalformed(): void
    {
        // setup
        $route = '/route/[i:id]';
        $router = new Router();

        $router->addRoute($route, function (string $route, $parameters) {
            return $parameters;
        });

        $router->registerMiddleware($route, function (string $route, array $parameters) {
            $parameters['_second'] = $parameters['id'];
            $parameters['id'] += 9;

            return [
                $route,
                $parameters
            ];
        });

        // This middleware is broken, don't parse the result
        $router->registerMiddleware($route, function (string $route, array $parameters) {
            $parameters['_dont_set_this'] = true;

            return null;
        });

        $router->registerMiddleware($route, function (string $route, array $parameters) {
            $parameters['_third'] = $parameters['id'];
            $parameters['id'] *= 2;

            return [
                $route,
                $parameters
            ];
        });

        $router->registerMiddleware('*', function (string $route, array $parameters) {
            $parameters['_first'] = $parameters['id'];
            $parameters['id'] *= 2;

            return [
                $route,
                $parameters
            ];
        });

        // test body
        $result = $router->callRoute('/route/1');

        // assertions
        $this->assertEquals(22, $result['id']);
        $this->assertEquals(1, $result['_first']);
        $this->assertEquals(2, $result['_second']);
        $this->assertEquals(11, $result['_third']);
        $this->assertTrue(empty($result['_dont_set_this']));
    }
}
