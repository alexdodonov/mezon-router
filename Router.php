<?php
namespace Mezon\Router;

// TODO compare speed with Symphony router
// TODO [create|edit:action]
// TODO /date/[i:year]-[i:month]-[i:day]

/**
 * Class Router
 *
 * @package Mezon
 * @subpackage Router
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/15)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Router class
 */
class Router
{

    /**
     * Method wich handles invalid route error
     *
     * @var callable
     */
    private $invalidRouteErrorHandler;

    /**
     * Set of routes
     *
     * @var \Mezon\Router\RoutesSet
     */
    private $routesSet = null;

    /**
     * URL parser
     *
     * @var \Mezon\Router\UrlParser
     */
    private $urlParser = null;

    /**
     * Method returns request method
     *
     * @return string Request method
     */
    protected function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $_SERVER['REQUEST_METHOD'] = $this->getRequestMethod();

        $this->invalidRouteErrorHandler = [
            $this,
            'noProcessorFoundErrorHandler'
        ];

        $this->routesSet = new RoutesSet();
        $this->urlParser = new UrlParser();
    }

    /**
     * Method fetches actions from the objects and creates GetRoutes for them
     *
     * @param object $object
     *            Object to be processed
     */
    public function fetchActions(object $object): void
    {
        $methods = get_class_methods($object);

        foreach ($methods as $method) {
            if (strpos($method, 'action') === 0) {
                $route = \Mezon\Router\Utils::convertMethodNameToRoute($method);
                $this->routesSet->addGetRoute($route, $object, $method);
                $this->routesSet->addPostRoute($route, $object, $method);
            }
        }
    }

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
     */
    public function addRoute(string $route, $callback, $requestMethod = 'GET'): void
    {
        $route = '/' . trim($route, '/') . '/';

        if (is_array($requestMethod)) {
            foreach ($requestMethod as $r) {
                $this->addRoute($route, $callback, $r);
            }
        } else {
            $routes = &$this->routesSet->getRoutesForMethod($requestMethod);
            // this 'if' is for backward compatibility
            // remove it on 02-04-2021
            if (is_array($callback) && isset($callback[1]) && is_array($callback[1])) {
                $callback = $callback[1];
            }
            $routes[$route] = $callback;
        }
    }

    /**
     * Method processes no processor found error
     *
     * @param string $route
     *            Route
     */
    public function noProcessorFoundErrorHandler(string $route)
    {
        throw (new \Exception(
            'The processor was not found for the route ' . $route . ' in ' . $this->routesSet->getAllRoutesTrace()));
    }

    /**
     * Method sets InvalidRouteErrorHandler function
     *
     * @param callable $function
     *            Error handler
     */
    public function setNoProcessorFoundErrorHandler(callable $function)
    {
        $oldErrorHandler = $this->invalidRouteErrorHandler;

        $this->invalidRouteErrorHandler = $function;

        return $oldErrorHandler;
    }

    /**
     * Processing specified router
     *
     * @param mixed $route
     *            Route
     */
    public function callRoute($route)
    {
        $route = \Mezon\Router\Utils::prepareRoute($route);

        if (($result = $this->urlParser->findStaticRouteProcessor(
            $this->routesSet->getRoutesForMethod($this->getRequestMethod()),
            $route)) !== false) {
            return $result;
        }

        if (($result = $this->urlParser->findDynamicRouteProcessor(
            $this->routesSet->getRoutesForMethod($this->getRequestMethod()),
            $route)) !== false) {
            return $result;
        }

        call_user_func($this->invalidRouteErrorHandler, $route);
    }

    /**
     * Method clears router data.
     */
    public function clear()
    {
        $this->routesSet->clear();
    }

    /**
     * Method returns route parameter
     *
     * @param string $name
     *            Route parameter
     * @return string Route parameter
     */
    public function getParam(string $name): string
    {
        return $this->urlParser->getParam($name);
    }

    /**
     * Does parameter exists
     *
     * @param string $name
     *            Param name
     * @return bool True if the parameter exists
     */
    public function hasParam(string $name): bool
    {
        return $this->urlParser->hasParam($name);
    }

    /**
     * Method returns true if the router exists
     *
     * @param string $route
     *            checking route
     * @return bool true if the router exists, false otherwise
     */
    public function routeExists(string $route): bool
    {
        return $this->routesSet->routeExists($route);
    }
}
