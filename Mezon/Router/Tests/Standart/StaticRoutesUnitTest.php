<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Standart;

use Mezon\Router\Tests\Base\StaticRoutesTestClass;
use Mezon\Router\RouterInterface;
use Mezon\Router\Tests\Base\BaseRouterUnitTestClass;
use Mezon\Router\Router;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class StaticRoutesUnitTest extends StaticRoutesTestClass
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
