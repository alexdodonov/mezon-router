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
     * Method handles integer type
     *
     * @param string $value
     *            value to be parsed
     * @return bool was the value parsed
     */
    public static function intHandler(string &$value): bool
    {
        if (is_numeric($value)) {
            $value = $value + 0;
            return true;
        }

        return false;
    }

    /**
     * Method handles command type
     *
     * @param string $value
     *            value to be parsed
     * @return bool was the value parsed
     */
    public static function commandHandler(string &$value): bool
    {
        if (preg_match('/^([a-z0-9A-Z_\/\-\.\@]+)$/', $value)) {
            return true;
        }

        return false;
    }

    /**
     * Method handles list of integers type
     *
     * @param string $value
     *            value to be parsed
     * @return bool was the value parsed
     */
    public static function intListHandler(string &$value): bool
    {
        if (preg_match('/^([0-9,]+)$/', $value)) {
            return true;
        }

        return false;
    }

    /**
     * Method handles string type
     *
     * @param string $value
     *            value to be parsed
     * @return bool was the value parsed
     */
    public static function stringHandler(string &$value): bool
    {
        $value = htmlspecialchars($value, ENT_QUOTES);

        return true;
    }

    /**
     * Init types
     */
    protected function initDefaultTypes()
    {
        $this->types['i'] = '\Mezon\Router\DefaultTypes::intHandler';
        $this->types['a'] = '\Mezon\Router\DefaultTypes::commandHandler';
        $this->types['il'] = '\Mezon\Router\DefaultTypes::intListHandler';
        $this->types['s'] = '\Mezon\Router\DefaultTypes::stringHandler';
    }
}
