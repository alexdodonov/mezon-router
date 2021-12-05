<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Standart;

use Mezon\Router\Router;
use Mezon\Router\RouterInterface;
use Mezon\Router\Tests\Base\ReverseRouteTestClass;
use Mezon\Router\Tests\Base\BaseRouterUnitTestClass;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ReverseRouteUnitTest extends ReverseRouteTestClass
{

    /**
     *
     * {@inheritdoc}
     * @see BaseRouterUnitTestClass::getRouter()
     */
    protected function getRouter(): RouterInterface
    {
        return new Router();
    }
}
