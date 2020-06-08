<?php
namespace Mezon\Router;

trait RoutesSet
{

    /**
     * List off routes for all supported request methods
     */
    protected $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'OPTION' => []
    ];

    /**
     * Route names
     *
     * @var array
     */
    private $routeNames = [];

    /**
     * This flag rises when we add route / * /
     *
     * @var bool
     */
    protected $universalRouteWasAdded = false;

    /**
     * Method validates request method
     *
     * @param string $requestMethod
     *            HTTP request method
     */
    protected function validateRequestMethod(string $requestMethod): void
    {
        if (isset($this->routes[$requestMethod]) === false) {
            throw (new \Exception('Unsupported request method'));
        }
    }

    /**
     * Method returns list of routes for the HTTP method.
     *
     * @param string $requestMethod
     *            HTTP request method
     * @return array Routes
     */
    protected function &getRoutesForMethod(string $requestMethod): array
    {
        return $this->routes[$requestMethod];
    }

    /**
     * Method returns a list of supported request methods
     *
     * @return array list of supported request methods
     */
    public function getListOfSupportedRequestMethods(): array
    {
        return [
            'GET',
            'POST',
            'PUT',
            'DELETE',
            'OPTION'
        ];
    }

    /**
     * Method clears router data.
     */
    public function clear()
    {
        $this->universalRouteWasAdded = false;

        $this->routeNames = [];

        foreach ($this->getListOfSupportedRequestMethods() as $requestMethod) {
            $this->routes[$requestMethod] = [];
        }
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
        $route = trim($route, '/');

        foreach ($this->getListOfSupportedRequestMethods() as $requestMethod) {
            if (isset($this->routes[$requestMethod][$route])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Method rturns all available routes
     */
    public function getAllRoutesTrace()
    {
        $trace = [];

        foreach ($this->getListOfSupportedRequestMethods() as $requestMethod) {
            if (count($this->routes[$requestMethod]) > 0) {
                $trace[] = $requestMethod . ':' . implode(', ', array_keys($this->routes[$requestMethod]));
            }
        }

        return implode('; ', $trace);
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
        $this->routes['GET'][trim($route, '/')] = [
            $object,
            $method
        ];
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
        $this->routes['POST'][trim($route, '/')] = [
            $object,
            $method
        ];
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
     * Validating that route name exists
     *
     * @param string $routeName
     * @return bool
     */
    protected function routeNameExists(string $routeName): bool
    {
        return isset($this->routeNames[$routeName]);
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
}
