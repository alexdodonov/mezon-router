<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Standart;

use Mezon\Router\RouterInterface;
use Mezon\Router\Router;
use Mezon\Router\Tests\Base\BaseRouterUnitTestClass;
use Mezon\Router\Tests\Base\BoolResultTestClass;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class BoolResultUnitTest extends BoolResultTestClass
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
