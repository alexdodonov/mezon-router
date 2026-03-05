<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

/**
 * @psalm-suppress PropertyNotSetInConstructor, UnusedClass
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
     * Testing method addGetRoute.
     * 
     * @psalm-suppress PossiblyUnusedMethod
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
     * Testing method addPostRoute.
     * 
     * @psalm-suppress PossiblyUnusedMethod
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
