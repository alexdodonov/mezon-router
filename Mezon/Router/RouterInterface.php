<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Interface RouterInterface
 *
 * @package Router
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
     * @param string[]|string $route
     *            route
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
     *            route
     * @param array{0: object, 1: string}|array{0: string, 1: string}|callable|string $callback
     *            collback wich will be processing route call.
     * @param string|string[] $requestMethod
     *            request type
     * @param string $routeName
     *            name of the route
     */
    public function addRoute(string $route, $callback, $requestMethod = 'GET', string $routeName = ''): void;

    /**
     * Method sets InvalidRouteErrorHandler function
     *
     * @param callable $function
     *            error handler
     *            
     * @return ?callable old error handler
     */
    public function setNoProcessorFoundErrorHandler(callable $function): ?callable;

    /**
     * Method returns true if the router exists
     *
     * @param string $route
     *            checking route
     * @return bool true if the router exists, false otherwise
     */
    public function routeExists(string $route): bool;

    /**
     * Method clears router data
     */
    public function clear(): void;

    /**
     * Getting route by name
     *
     * @param string $routeName
     *            route's name
     * @return string route
     */
    public function getRouteByName(string $routeName): string;

    /**
     * Compiling route into URL
     *
     * @param string $routeName
     *            route name
     * @param string[] $parameters
     *            parameters to use in URL
     * @return string compiled route
     */
    public function reverse(string $routeName, array $parameters = []): string;

    /**
     * Method registeres middleware for the router
     *
     * @param callable $middleware
     *            middleware
     */
    public function registerMiddleware(string $router, callable $middleware): void;

    /**
     * Method returns route parameter
     *
     * @param string $name
     *            route parameter
     * @return string route parameter
     */
    public function getParam(string $name): string;

    /**
     * Does parameter exists
     *
     * @param string $name
     *            param name
     * @return bool true if the parameter exists
     */
    public function hasParam(string $name): bool;

    /**
     * Method adds custom type
     *
     * @param string $typeName
     *            type name
     * @param string $className
     *            name of the class wich represents custom type
     */
    public function addType(string $typeName, string $className): void;

    /**
     * Method searches route processor
     *
     * @param string $route
     *            route
     * @return mixed|false result of the router processor
     */
    public function findStaticRouteProcessor(string $route);
}