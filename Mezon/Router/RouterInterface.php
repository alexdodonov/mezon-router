<?php
namespace Mezon\Router;

/**
 * Interface RouterInterface
 *
 * @package Mezon
 * @subpackage Router
 * @author Dodonov A.A.
 * @version v.1.0 (2021/09/27)
 * @copyright Copyright (c) 2021, aeon.org
 */

/**
 * Router interface
 */
interface RouterInterface
{

    /**
     * Processing specified router
     *
     * @param mixed $route
     *            Route
     * @return mixed value returned by route handler
     */
    public function callRoute($route);

    /**
     * Additing route for GET request
     *
     * @param string $route
     *            route
     * @param object $object
     *            callback object
     * @param string $method
     *            callback method
     */
    public function addPostRoute(string $route, object $object, string $method): void;

    /**
     * Additing route for GET request
     *
     * @param string $route
     *            route
     * @param object $object
     *            callback object
     * @param string $method
     *            callback method
     */
    public function addGetRoute(string $route, object $object, string $method): void;

    /**
     * Method adds route and it's handler
     *
     * $callback function may have two parameters - $route and $parameters. Where $route is a called route,
     * and $parameters is associative array (parameter name => parameter value) with URL parameters
     *
     * @param string $route
     *            Route
     * @param mixed $callback
     *            Collback wich will be processing route call.
     * @param string|array $requestMethod
     *            Request type
     * @param string $routeName
     *            name of the route
     */
    public function addRoute(string $route, $callback, $requestMethod = 'GET', string $routeName = ''): void;

    /**
     * Method sets InvalidRouteErrorHandler function
     *
     * @param callable $function
     *            Error handler
     *            
     * @return callable old error handler
     */
    public function setNoProcessorFoundErrorHandler(callable $function): callable;
}