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
     * Method handles list of integers type
     *
     * @param string $value
     *            value to be parsed
     * @return bool was the value parsed
     */
    public static function handle(string &$value): bool
    {
        if (preg_match('/^([0-9,]+)$/', $value)) {
            return true;
        }

        return false;
    }
}
