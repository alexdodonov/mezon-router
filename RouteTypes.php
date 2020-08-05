<?php
namespace Mezon\Router;

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
     * @var array
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
}
