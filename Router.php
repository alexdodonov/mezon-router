<?php
namespace Mezon\Router;

// - installation
// - add in mezon/mezon documentation
// TODO compare speed with klein
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
     * Mapping of routes to their execution functions for GET requests
     *
     * @var array
     */
    private $getRoutes = [];

    /**
     * Mapping of routes to their execution functions for GET requests
     *
     * @var array
     */
    private $postRoutes = [];

    /**
     * Mapping of routes to their execution functions for PUT requests
     *
     * @var array
     */
    private $putRoutes = [];

    /**
     * Mapping of routes to their execution functions for DELETE requests
     *
     * @var array
     */
    private $deleteRoutes = [];

    /**
     * Method wich handles invalid route error
     *
     * @var array
     */
    private $invalidRouteErrorHandler = [];

    /**
     * Parsed parameters of the calling router
     *
     * @var array
     */
    protected $parameters = [];

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
    function __construct()
    {
        $_SERVER['REQUEST_METHOD'] = $this->getRequestMethod();

        $this->invalidRouteErrorHandler = [
            $this,
            'noProcessorFoundErrorHandler'
        ];
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
                $this->getRoutes["/$route/"] = [
                    $object,
                    $method
                ];
                $this->postRoutes["/$route/"] = [
                    $object,
                    $method
                ];
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
     * @param string $requestMethod
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
            $routes = &$this->_getRoutesForMethod($requestMethod);
            $routes[$route] = $callback;
        }
    }

    /**
     * Method searches route processor
     *
     * @param mixed $processors
     *            Callable router's processor
     * @param string $route
     *            Route
     * @return mixed Result of the router processor
     */
    private function _findStaticRouteProcessor(&$processors, string $route)
    {
        foreach ($processors as $i => $processor) {
            // exact router or 'all router'
            if ($i == $route || $i == '/*/') {
                if (is_callable($processor) && is_array($processor) === false) {
                    return $processor($route, []);
                }

                $functionName = $processor[1];

                if (is_callable($processor) &&
                    (method_exists($processor[0], $functionName) || isset($processor[0]->$functionName))) {
                    // passing route path and parameters
                    return call_user_func($processor, $route, []);
                } elseif (method_exists($processor[0], $functionName) === false) {
                    $callableDescription = \Mezon\Router\Utils::getCallableDescription($processor);

                    throw (new \Exception("'$callableDescription' does not exists"));
                } else {
                    $callableDescription = \Mezon\Router\Utils::getCallableDescription($processor);

                    throw (new \Exception("'$callableDescription' must be callable entity"));
                }
            }
        }

        return false;
    }

    /**
     * Method returns list of routes for the HTTP method.
     *
     * @param string $method
     *            HTTP Method
     * @return array Routes
     */
    private function &_getRoutesForMethod(string $method): array
    {
        switch ($method) {
            case ('GET'):
                $result = &$this->getRoutes;
                break;

            case ('POST'):
                $result = &$this->postRoutes;
                break;

            case ('PUT'):
                $result = &$this->putRoutes;
                break;

            case ('DELETE'):
                $result = &$this->deleteRoutes;
                break;

            default:
                throw (new \Exception('Unsupported request method'));
        }

        return $result;
    }

    /**
     * Method tries to process static routes without any parameters
     *
     * @param string $route
     *            Route
     * @return mixed Result of the router processor
     */
    private function _tryStaticRoutes($route)
    {
        $routes = $this->_getRoutesForMethod($this->getRequestMethod());

        return $this->_findStaticRouteProcessor($routes, $route);
    }

    /**
     * Matching parameter and component
     *
     * @param mixed $component
     *            Component of the URL
     * @param string $parameter
     *            Parameter to be matched
     * @return string Matched url parameter
     */
    private function _matchParameterAndComponent(&$component, string $parameter)
    {
        $parameterData = explode(':', trim($parameter, '[]'));
        $return = false;

        switch ($parameterData[0]) {
            case ('i'):
                if (is_numeric($component)) {
                    $component = $component + 0;
                    $return = $parameterData[1];
                }
                break;
            case ('a'):
                if (preg_match('/^([a-z0-9A-Z_\/\-\.\@]+)$/', $component)) {
                    $return = $parameterData[1];
                }
                break;
            case ('il'):
                if (preg_match('/^([0-9,]+)$/', $component)) {
                    $return = $parameterData[1];
                }
                break;
            case ('s'):
                $component = htmlspecialchars($component, ENT_QUOTES);
                $return = $parameterData[1];
                break;
            default:
                throw (new \Exception('Illegal parameter type/value : ' . $parameterData[0]));
        }

        return $return;
    }

    /**
     * Method matches route and pattern
     *
     * @param array $cleanRoute
     *            Cleaned route splitted in parts
     * @param array $cleanPattern
     *            Route pattern
     * @return array Array of route's parameters
     */
    private function _matchRouteAndPattern(array $cleanRoute, array $cleanPattern)
    {
        if (count($cleanRoute) !== count($cleanPattern)) {
            return false;
        }

        $paremeters = [];

        for ($i = 0; $i < count($cleanPattern); $i ++) {
            if (\Mezon\Router\Utils::isParameter($cleanPattern[$i])) {
                $parameterName = $this->_matchParameterAndComponent($cleanRoute[$i], $cleanPattern[$i]);

                // it's a parameter
                if ($parameterName !== false) {
                    // parameter was matched, store it!
                    $paremeters[$parameterName] = $cleanRoute[$i];
                } else {
                    return false;
                }
            } else {
                // it's a static part of the route
                if ($cleanRoute[$i] !== $cleanPattern[$i]) {
                    return false;
                }
            }
        }

        $this->parameters = $paremeters;
    }

    /**
     * Method searches dynamic route processor
     *
     * @param array $processors
     *            Callable router's processor
     * @param string $route
     *            Route
     * @return string Result of the router'scall or false if any error occured
     */
    private function _findDynamicRouteProcessor(array &$processors, string $route)
    {
        $cleanRoute = explode('/', trim($route, '/'));

        foreach ($processors as $i => $processor) {
            $cleanPattern = explode('/', trim($i, '/'));

            if ($this->_matchRouteAndPattern($cleanRoute, $cleanPattern) !== false) {
                return call_user_func($processor, $route, $this->parameters); // return result of the router
            }
        }

        return false;
    }

    /**
     * Method tries to process dynamic routes with parameters
     *
     * @param string $route
     *            Route
     * @return string Result of the route call
     */
    private function _tryDynamicRoutes(string $route)
    {
        switch ($this->getRequestMethod()) {
            case ('GET'):
                $result = $this->_findDynamicRouteProcessor($this->getRoutes, $route);
                break;

            case ('POST'):
                $result = $this->_findDynamicRouteProcessor($this->postRoutes, $route);
                break;

            case ('PUT'):
                $result = $this->_findDynamicRouteProcessor($this->putRoutes, $route);
                break;

            case ('DELETE'):
                $result = $this->_findDynamicRouteProcessor($this->deleteRoutes, $route);
                break;

            default:
                throw (new \Exception('Unsupported request method'));
        }

        return $result;
    }

    /**
     * Method rturns all available routes
     */
    private function _getAllRoutesTrace()
    {
        return (count($this->getRoutes) ? 'GET:' . implode(', ', array_keys($this->getRoutes)) . '; ' : '') .
            (count($this->postRoutes) ? 'POST:' . implode(', ', array_keys($this->postRoutes)) . '; ' : '') .
            (count($this->putRoutes) ? 'PUT:' . implode(', ', array_keys($this->putRoutes)) . '; ' : '') .
            (count($this->deleteRoutes) ? 'DELETE:' . implode(', ', array_keys($this->deleteRoutes)) : '');
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
            'The processor was not found for the route ' . $route . ' in ' . $this->_getAllRoutesTrace()));
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
     * @param string $route
     *            Route
     */
    public function callRoute($route)
    {
        $route = \Mezon\Router\Utils::prepareRoute($route);

        if (($result = $this->_tryStaticRoutes($route)) !== false) {
            return $result;
        }

        if (($result = $this->_tryDynamicRoutes($route)) !== false) {
            return $result;
        }

        call_user_func($this->invalidRouteErrorHandler, $route);
    }

    /**
     * Method clears router data.
     */
    public function clear()
    {
        $this->getRoutes = [];

        $this->postRoutes = [];

        $this->putRoutes = [];

        $this->deleteRoutes = [];
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
        if (isset($this->parameters[$name]) === false) {
            throw (new \Exception('Paremeter ' . $name . ' was not found in route', - 1));
        }

        return $this->parameters[$name];
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
        return isset($this->parameters[$name]);
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
        $allRoutes = array_merge($this->deleteRoutes, $this->putRoutes, $this->postRoutes, $this->getRoutes);

        return (isset($allRoutes[$route]));
    }
}
