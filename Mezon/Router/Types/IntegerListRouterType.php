<?php
namespace Mezon\Router\Types;

/**
 * Default integer list type for router
 *
 * @author gdever
 */
class IntegerListRouterType
{

    /**
     * Method returns regexp for searching
     *
     * @return string regexp for searching
     */
    public static function searchRegExp(): string
    {
        return '(\[il:'.BaseType::PARAMETER_NAME_REGEXP.'\])';
    }

    /**
     * Method returns regexp for parsing
     *
     * @return string regexp for parsing
     */
    public static function parserRegExp(): string
    {
        return '[0-9,]+';
    }
}
