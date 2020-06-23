<?php
namespace Mezon\Router\Types;

/**
 * Default integer type for router
 *
 * @author gdever
 */
class IntegerRouterType
{

    /**
     * Method handles integer type
     *
     * @param string $value
     *            value to be parsed
     * @return bool was the value parsed
     */
    public static function handle(string &$value): bool
    {
        if (is_numeric($value)) {
            $value = $value + 0;
            return true;
        }

        return false;
    }
}
