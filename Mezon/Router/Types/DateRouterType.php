<?php
namespace Mezon\Router\Types;

/**
 * Default date type for router
 *
 * @author gdever
 * @psalm-suppress UnusedClass
 */
class DateRouterType
{

    /**
     * Method returns regexp for searching
     *
     * @return string regexp for searching
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function searchRegExp(): string
    {
        return '(\[date:'.BaseType::PARAMETER_NAME_REGEXP.'\])';
    }

    /**
     * Method returns regexp for parsing
     *
     * @return string regexp for parsing
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function parserRegExp(): string
    {
        return '[0-9]{4}-[0-9]{2}-[0-9]{2}';
    }
}
