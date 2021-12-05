<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Standart;

use Mezon\Router\Router;
use Mezon\Router\RouterInterface;
use Mezon\Router\Tests\Base\DynamicRoutesInvalidCasesTestClass;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class DynamicRoutesInvalidCasesUnitTest extends DynamicRoutesInvalidCasesTestClass
{

    protected function getRouter(): RouterInterface
    {
        return new Router();
    }
}
