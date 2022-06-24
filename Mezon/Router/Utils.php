<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Class Utils
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2020/01/17)
 * @copyright Copyright (c) 2020, http://aeon.su
 */

/**
 * Router utilities class
 */
class Utils
{

    /**
     * Converting method name to route
     *
     * @param string $methodName
     *            method name
     * @return string route
     */
    public static function convertMethodNameToRoute(string $methodName): string
    {
        $methodName = str_replace('action', '', $methodName);

        if (ctype_upper($methodName[0])) {
            $methodName[0] = strtolower($methodName[0]);
        }

        for ($i = 1; $i < strlen($methodName); $i ++) {
            if (ctype_upper($methodName[$i])) {
                $methodName = substr_replace($methodName, '-' . strtolower($methodName[$i]), $i, 1);
            }
        }

        return $methodName;
    }

    /**
     * Method prepares route for the next processing
     *
     * @param string[]|string $route
     *            route
     * @return string trimmed route
     * @psalm-suppress MixedArgumentTypeCoercion, MixedAssignment
     */
    public static function prepareRoute($route): string
    {
        if (is_array($route) && $route[0] === '') {
            $route = (string)$_SERVER['REQUEST_URI'];
        }

        if ($route === '/') {
            $route = 'index';
        }

        if (is_array($route)) {
            $route = implode('/', $route);
        }

        $route = urldecode($route);

        /** @var string $route */
        return trim($route, '/');
    }

    /**
     * Method compiles callable description
     *
     * @param
     *            string|mixed|array{0:string, 1:string} $processor
     *            object to be descripted
     * @return string description
     * @psalm-suppress MixedOperand, MixedArrayAccess, MixedArgument, MissingParamType
     */
    public static function getCallableDescription($processor): string
    {
        if (is_string($processor)) {
            return $processor;
        } elseif (isset($processor[0]) && isset($processor[1])) {
            if (is_object($processor[0])) {
                return get_class($processor[0]) . '::' . $processor[1];
            } elseif (is_string($processor[0])) {
                return $processor[0] . '::' . $processor[1];
            }
        }

        return serialize($processor);
    }
}
