<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Simple;

use Mezon\Router\RouterInterface;
use Mezon\Router\Tests\Base\ExtractParametersTestClass;
use Mezon\Router\Tests\Base\BaseRouterUnitTestClass;
use Mezon\Router\SimpleRouter;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ExtractParametersUnitTest extends ExtractParametersTestClass
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
