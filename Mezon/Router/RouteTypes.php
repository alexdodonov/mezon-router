<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Trait RouteTypes
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/15)
 * @copyright Copyright (c) 2019, http://aeon.su
 */

/**
 * Default types for router - integer, string, command, list if ids
 *
 * @author gdever
 */
trait RouteTypes
{

    /**
     * Supported types of URL parameters
     *
     * @var array<string, string>
     */
    private $types = [];

    /**
     * Init types
     */
    private function initDefaultTypes(): void
    {
        $this->types['i'] = '\Mezon\Router\Types\IntegerRouterType';
        $this->types['a'] = '\Mezon\Router\Types\CommandRouterType';
        $this->types['il'] = '\Mezon\Router\Types\IntegerListRouterType';
        $this->types['s'] = '\Mezon\Router\Types\StringRouterType';
        $this->types['fp'] = '\Mezon\Router\Types\FixPointNumberRouterType';
    }

    /**
     * Method adds custom type
     *
     * @param string $typeName
     *            type name
     * @param string $className
     *            name of the class wich represents custom type
     */
    public function addType(string $typeName, string $className): void
    {
        $this->types = array_merge([
            $typeName => $className
        ], $this->types);
    }
}
