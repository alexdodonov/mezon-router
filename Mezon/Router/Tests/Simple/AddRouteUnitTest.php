<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Simple;

use Mezon\Router\RouterInterface;
use Mezon\Router\SimpleRouter;
use Mezon\Router\Tests\Base\RouterUnitTestClass;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor, UnusedClass
 */
class AddRouteUnitTest extends RouterUnitTestClass
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
