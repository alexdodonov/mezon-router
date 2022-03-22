<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Standart;

use Mezon\Router\Router;
use Mezon\Router\RouterInterface;
use Mezon\Router\Tests\Base\NonAsciiCharsUnitTestClass;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class NonAsciiCharsUnitTest extends NonAsciiCharsUnitTestClass
{

    /**
     *
     * {@inheritdoc}
     * @see NonAsciiCharsUnitTestClass::getRouter()
     */
    protected function getRouter(): RouterInterface
    {
        return new Router();
    }
}
