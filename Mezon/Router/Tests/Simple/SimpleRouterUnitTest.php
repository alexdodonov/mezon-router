<?php
namespace Mezon\Router\Tests\Simple;

use Mezon\Router\RouterInterface;
use Mezon\Router\SimpleRouter;
use Mezon\Router\Tests\Base\RouterUnitTestClass;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class SimpleRouterUnitTest extends RouterUnitTestClass
{

    /**
     *
     * {@inheritdoc}
     * @see RouterUnitTestClass::getRouter()
     */
    protected function getRouter(): RouterInterface
    {
        return new SimpleRouter();
    }
}
