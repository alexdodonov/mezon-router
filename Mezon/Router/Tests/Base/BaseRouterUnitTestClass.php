<?php
declare(strict_types = 1);
namespace Mezon\Router\Tests\Base;

use PHPUnit\Framework\TestCase;
use Mezon\Router\RouterInterface;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
abstract class BaseRouterUnitTestClass extends TestCase
{

    /**
     * Method creates router object
     * 
     * @return RouterInterface
     */
    protected abstract function getRouter(): RouterInterface;
}