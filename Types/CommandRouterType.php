<?php
namespace Mezon\Router\Types;

/**
 * Default command type for router
 *
 * @author gdever
 */
class CommandRouterType
{

    /**
     * Method handles command type
     *
     * @param string $value
     *            value to be parsed
     * @return bool was the value parsed
     */
    public static function handle(string &$value): bool
    {
        if (preg_match('/^([a-z0-9A-Z_\/\-\.\@]+)$/', $value)) {
            return true;
        }

        return false;
    }
}
