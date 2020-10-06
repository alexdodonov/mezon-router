<?php
namespace Mezon\Router\Types;

/**
 * Fix point number type for router
 *
 * @author gdever
 */
class FixPointNumberRouterType
{

    /**
     * Method returns regexp for searching
     *
     * @return string regexp for searching
     */
    public static function searchRegExp(): string
    {
        return '(\[fp:'.BaseType::PARAMETER_NAME_REGEXP.'+\])';
    }

    /**
     * Method returns regexp for parsing
     *
     * @return string regexp for parsing
     */
    public static function parserRegExp(): string
    {
        return '[-+]{0,1}[0-9]+.[0-9]+';
    }
}
