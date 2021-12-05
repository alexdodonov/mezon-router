<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

use PHPUnit\Framework\TestCase;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class MiddlewareTestClass extends BaseRouterUnitTestClass
{

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
     * Testing middleware
     */
    public function testMiddleware(): void
    {
        // setup
        $route = '/route-with-middleware/';
        $router = $this->getRouter();
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
        /** @var array{0: int, 1: int} $result */
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
        $router = $this->getRouter();

        $router->addRoute($route, function (string $route, array $parameters): array {
            return $parameters;
        });

        $router->registerMiddleware($route, /**
         *
         * @psalm-param string $route route
         * @psalm-param array{id: int} $parameters parameters
         */
        function (string $route, array $parameters) {
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

        $router->registerMiddleware($route, /**
         *
         * @psalm-param string $route
         *            route
         * @psalm-param array{id: int} $parameters parameters
         */
        function (string $route, array $parameters) {
            $parameters['_third'] = $parameters['id'];
            $parameters['id'] *= 2;

            return [
                $route,
                $parameters
            ];
        });

        $router->registerMiddleware('*', /**
         *
         * @psalm-param string $route
         *            route
         * @psalm-param array{id: int} $parameters parameters
         */
        function (string $route, array $parameters) {
            $parameters['_first'] = $parameters['id'];
            $parameters['id'] *= 2;

            return [
                $route,
                $parameters
            ];
        });

        // test body
        /** @var array<string, mixed> $result */
        $result = $router->callRoute('/route/1');

        // assertions
        $this->assertEquals(22, $result['id']);
        $this->assertEquals(1, $result['_first']);
        $this->assertEquals(2, $result['_second']);
        $this->assertEquals(11, $result['_third']);
        $this->assertTrue(empty($result['_dont_set_this']));
    }
}
