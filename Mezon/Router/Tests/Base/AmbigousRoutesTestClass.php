<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

use PHPUnit\Framework\TestCase;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class AmbigousRoutesTestClass extends BaseRouterUnitTestClass
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
     * Testing ambigous routes
     */
    public function testAmbigousRoutes(): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute('/user/[s:login]', function () {
            return '1';
        });
        $router->addRoute('/user/[s:login]/[s:regdate]', function () {
            return '2';
        });

        // test body
        /** @var string $result */
        $result = $router->callRoute('/user/index@localhost');

        // assertions
        $this->assertEquals('1', $result);
    }

    /**
     * Testing route with date
     */
    public function testDateRoute(): void
    {
        // setup
        $router = $this->getRouter();
        $router->addRoute('/forum-[i:year]-[i:month]-[i:day]', function () {
            return '1';
        });

        // test body
        /** @var string $result */
        $result = $router->callRoute('/forum-2022-09-13');

        // assertions
        $this->assertEquals('1', $result);
    }
}
