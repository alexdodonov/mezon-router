<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class UnexistingRouteTestClass extends BaseRouterUnitTestClass
{

    /**
     * Testing default unexisting route handler
     */
    public function testUnexistingRouteException(): void
    {
        // setup
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $router = $this->getRouter();
        $router->addGetRoute('r1', $this, 'r1');
        $router->addPostRoute('r1', $this, 'r1');

        // assertions
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(- 1);
        $this->expectErrorMessage('The processor was not found for the route unexisting in GET : r1, ; POST : r1, ; PUT : <none>; DELETE : <none>; OPTION : <none>; PATCH : <none>');

        // test body
        $router->callRoute('unexisting');
    }
}
