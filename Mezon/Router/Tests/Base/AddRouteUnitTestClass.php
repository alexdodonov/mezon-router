<?php
namespace Mezon\Router\Tests\Base;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class AddRouteUnitTestClass extends BaseRouterUnitTestClass
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
        $router = $this->getRouter();
        $route = 'route';
        RouterUnitTestUtils::setRequestMethod('GET');

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
        $router = $this->getRouter();
        $route = 'route';
        RouterUnitTestUtils::setRequestMethod('POST');

        // test body
        $router->addPostRoute($route, $this, 'actionRoute');

        // assertions
        $this->assertEquals(1, $router->callRoute($route));
    }
}
