<?php
namespace Mezon\Router\Types;

/**
 * Default string type for router
 *
 * @author gdever
 */
class StringRouterType
{

    /**
     * Method returns regexp for searching
     *
     * @return string regexp for searching
     */
    public static function searchRegExp(): string
    {
        return '(\[s:'.BaseType::PARAMETER_NAME_REGEXP.'\])';
    }

    /**
     * Method returns regexp for parsing
     *
     * @return string regexp for parsing
     */
    public static function parserRegExp(): string
    {
        return '[a-z0-9A-Z_\-.@%&:;,\s]+';
    }
}
