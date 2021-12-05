<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Simple;

use Mezon\Router\RouterInterface;
use Mezon\Router\SimpleRouter;
use Mezon\Router\Tests\Base\DynamicRoutesTestClass;
use Mezon\Router\Tests\Base\BaseRouterUnitTestClass;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class DynamicRoutesUnitTest extends DynamicRoutesTestClass
{

    /**
     *
     * {@inheritdoc}
     * @see BaseRouterUnitTestClass::getRouter()
     */
    protected function getRouter(): RouterInterface
    {
        return new SimpleRouter();
    }
}
