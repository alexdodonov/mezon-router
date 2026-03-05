<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Simple;

use Mezon\Router\SimpleRouter;
use Mezon\Router\Tests\Base\UnexistingRouteTestClass;
use Mezon\Router\RouterInterface;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor, UnusedClass
 */
class UnexistingRouteUnitTest extends UnexistingRouteTestClass
{

    /**
     *
     * {@inheritdoc}
     * @see UnexistingRouteTestClass::getRouter()
     */
    protected function getRouter(): RouterInterface
    {
        return new SimpleRouter();
    }
}
