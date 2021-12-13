<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Standart;

use Mezon\Router\RouterInterface;
use Mezon\Router\Tests\Base\RouterUnitTestClass;
use Mezon\Router\Router;
use Mezon\Router\Tests\Base\StaticMethodsCallsTestClass;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class StaticMethodsCallsUnitTest extends StaticMethodsCallsTestClass
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
