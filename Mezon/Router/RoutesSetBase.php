<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Trait RoutesSetBase
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2021/09/27)
 * @copyright Copyright (c) 2021, http://aeon.su
 */
trait RoutesSetBase
{

    /**
     * Method returns true if the param router exists
     *
     * @param string $route
     *            checking route
     * @param string $requestMethod
     *            HTTP request method
     * @return bool true if the param router exists, false otherwise
     */
    protected abstract function paramRouteExists(string $route, string $requestMethod): bool;

    /**
     * Method returns true if the router exists
     *
     * @param string $route
     *            checking route
     * @return bool true if the router exists, false otherwise
     */
    public function routeExists(string $route): bool
    {
        $route = trim($route, '/');

        foreach (SuppportedRequestMethods::getListOfSupportedRequestMethods() as $requestMethod) {
            if (isset($this->staticRoutes[$requestMethod][$route])) {
                return true;
            } else {
                if ($this->paramRouteExists($route, $requestMethod)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Route names
     *
     * @var string[]
     */
    private $routeNames = [];

    /**
     * Method clears other data
     */
    protected function clearOtherData(): void
    {}

    /**
     * Method clears router data
     */
    public function clear(): void
    {
        $this->routeNames = [];

        foreach (SuppportedRequestMethods::getListOfSupportedRequestMethods() as $requestMethod) {
            $this->staticRoutes[$requestMethod] = [];
            $this->paramRoutes[$requestMethod] = [];
        }

        $this->middleware = [];

        $this->clearOtherData();
    }

    /**
     * Getting route by name
     *
     * @param string $routeName
     *            route's name
     * @return string route
     */
    public function getRouteByName(string $routeName): string
    {
        if ($this->routeNameExists($routeName) === false) {
            throw (new \Exception('Route with name ' . $routeName . ' does not exist'));
        }

        return $this->routeNames[$routeName];
    }

    /**
     * Validating that route name exists
     *
     * @param string $routeName route name
     * @return bool true if the route exists, false otherwise
     */
    protected function routeNameExists(string $routeName): bool
    {
        return isset($this->routeNames[$routeName]);
    }

    /**
     * Method registers name of the route
     *
     * @param string $routeName
     *            route's name
     * @param string $route
     *            route
     */
    protected function registerRouteName(string $routeName, string $route): void
    {
        if ($routeName != '') {
            $this->routeNames[$routeName] = $route;
        }
    }

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
    public function addGetRoute(string $route, object $object, string $method): void
    {
        $this->addRoute($route, [
            $object,
            $method
        ], 'GET');
    }

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
    public function addPostRoute(string $route, object $object, string $method): void
    {
        $this->addRoute($route, [
            $object,
            $method
        ], 'POST');
    }

    /**
     * Method adds route and it's handler
     *
     * $callback function may have two parameters - $route and $parameters. Where $route is a called route,
     * and $parameters is associative array (parameter name => parameter value) with URL parameters
     *
     * @param string $route
     *            route
     * @param array{0: object, 1: string}|array{0: string, 1: string}|callable|string $callback
     *            callback wich will be processing route call.
     * @param string|string[] $requestMethod
     *            request type
     * @param string $routeName
     *            name of the route
     */
    public function addRoute(string $route, $callback, $requestMethod = 'GET', string $routeName = ''): void
    {
        $route = Utils::prepareRoute($route);

        if (is_array($requestMethod)) {
            foreach ($requestMethod as $r) {
                $this->addRoute($route, $callback, $r, $routeName);
            }
        } else {
            SuppportedRequestMethods::validateRequestMethod($requestMethod);

            if (strpos($route, '[') === false) {
                $this->staticRoutes[$requestMethod][$route] = $callback;
            } else {
                $this->addParamRoute($requestMethod, $route, $callback);
            }
            // register route name
            $this->registerRouteName($routeName, $route);
        }
    }

    /**
     * Compiling route into URL
     *
     * @param string $routeName
     *            route name
     * @param string[] $parameters
     *            parameters to use in URL
     * @return string compiled route
     */
    public function reverse(string $routeName, array $parameters = []): string
    {
        $route = $this->getRouteByName($routeName);

        foreach ($parameters as $name => $value) {
            $route = preg_replace('/\[([A-Za-z_\-])\:' . $name . ']/', $value, $route);
        }

        return $route;
    }
}
