<?php
namespace Mezon\Router;

/**
 * Default types for router - integer, string, command, list if ids
 *
 * @author gdever
 */
trait DefaultTypes
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
    protected function initDefaultTypes()
    {
        $this->types['i'] = '\Mezon\Router\Types\IntegerRouterType::handle';
        $this->types['a'] = '\Mezon\Router\Types\CommandRouterType::handle';
        $this->types['il'] = '\Mezon\Router\Types\IntegerListRouterType::handle';
        $this->types['s'] = '\Mezon\Router\Types\StringRouterType::handle';
    }
}
