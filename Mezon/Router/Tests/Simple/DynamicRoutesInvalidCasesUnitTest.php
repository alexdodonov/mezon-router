<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Simple;

use Mezon\Router\RouterInterface;
use Mezon\Router\SimpleRouter;
use Mezon\Router\Tests\Base\DynamicRoutesInvalidCasesTestClass;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class DynamicRoutesInvalidCasesUnitTest extends DynamicRoutesInvalidCasesTestClass
{

    protected function getRouter(): RouterInterface
    {
        return new SimpleRouter();
    }
}
