<?php
namespace Mezon\Router\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Router\Router;

class AddRouteUnitTest extends TestCase
{

    /**
     * Some action
     *
     * @return int
     */
    public function actionRoute(): int
    {
        return 1;
    }

    /**
     * Testing method addGetRoute
     */
    public function testAddGetRoute(): void
    {
        // setup
        $router = new Router();
        $route = 'route';
        RouterUnitTest::setRequestMethod('GET');

        // test body
        $router->addGetRoute($route, $this, 'actionRoute');

        // assertions
        $this->assertEquals(1, $router->callRoute($route));
    }

    /**
     * Testing method addPostRoute
     */
    public function testAddPostRoute(): void
    {
        // setup
        $router = new Router();
        $route = 'route';
        RouterUnitTest::setRequestMethod('POST');

        // test body
        $router->addPostRoute($route, $this, 'actionRoute');

        // assertions
        $this->assertEquals(1, $router->callRoute($route));
    }
}
