<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests;

use Mezon\Router\Router;
use Mezon\Router\RouterInterface;
use Mezon\Router\Tests\Base\RouterUnitTestClass;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class RouterUnitTest extends RouterUnitTestClass
{

    /**
     *
     * {@inheritdoc}
     * @see RouterUnitTestClass::getRouter()
     */
    protected function getRouter(): RouterInterface
    {
        return new Router();
    }
}
