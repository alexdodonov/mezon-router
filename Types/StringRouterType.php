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
     * Method handles string type
     *
     * @param string $value
     *            value to be parsed
     * @return bool was the value parsed
     */
    public static function handle(string &$value): bool
    {
        $value = htmlspecialchars($value, ENT_QUOTES);

        return true;
    }
}
