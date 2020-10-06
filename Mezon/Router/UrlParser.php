<?php
namespace Mezon\Router;

use Mezon\Router\Types\BaseType;

trait UrlParser
{

    /**
     * Parsed parameters of the calling router
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Called route
     *
     * @var string
     */
    protected $calledRoute = '';

    /**
     * Cache for regular expressions
     *
     * @var array
     */
    private $cachedRegExps = [];

    /**
     * Cached parameters for route
     *
     * @var array
     */
    private $cachedParameters = [];

    /**
     * Middleware for routes processing
     *
     * @var array
     */
    private $middleware = [];

    /**
     * Method compiles route pattern string in regex string.
     * For example [i:id]/some-str in ([\[0-9\]])/some-str
     *
     * @param string $routerPattern
     *            router pattern
     * @return string regexp pattern
     */
    private function _getRouteMatcherRegExPattern(string $routerPattern, bool $addBraces = true): string
    {
        $key = $routerPattern . ($addBraces ? '1' : '0');

        // try read from cache
        if (isset($this->cachedRegExps[$key])) {
            return $this->cachedRegExps[$key];
        }

        // parsing routes
        $compiledRouterPattern = $routerPattern;
        foreach ($this->types as $typeClass) {
            $compiledRouterPattern = preg_replace(
                '/' . $typeClass::searchRegExp() . '/',
                ($addBraces ? '(' : '') . $typeClass::parserRegExp() . ($addBraces ? ')' : ''),
                $compiledRouterPattern);
        }

        // final setup + save in cache
        $this->cachedRegExps[$key] = $compiledRouterPattern;

        return $compiledRouterPattern;
    }

    /**
     * Method returns all parameter names in the route
     *
     * @param string $routerPattern
     *            route
     * @return array names
     */
    private function _getParameterNames(string $routerPattern): array
    {
        if (isset($this->cachedParameters[$routerPattern])) {
            return $this->cachedParameters[$routerPattern];
        }

        $regExPattern = [];

        foreach (array_keys($this->types) as $typeName) {
            $regExPattern[] = $typeName;
        }

        $regExPattern = '\[(' . implode('|', $regExPattern) . '):(' . BaseType::PARAMETER_NAME_REGEXP . ')\]';

        $names = [];
        preg_match_all('/' . str_replace('/', '\\/', $regExPattern) . '/', $routerPattern, $names);

        $return = [];

        foreach ($names[2] as $name) {
            $return[] = $name;
        }

        $this->cachedParameters[$routerPattern] = $return;

        return $return;
    }

    /**
     * Method warms cache
     */
    public function warmCache(): void
    {
        foreach (self::getListOfSupportedRequestMethods() as $requestMethod) {
            $routesForMethod = $this->paramRoutes[$requestMethod];

            foreach (array_keys($routesForMethod) as $routerPattern) {
                $this->_getRouteMatcherRegExPattern($routerPattern);

                $this->_getParameterNames($routerPattern);
            }
        }
    }

    /**
     * Method searches dynamic route processor
     *
     * @param string $route
     *            Route
     * @param string $requestMethod
     *            Request method
     * @return array|callable|bool route's handler or false in case the handler was not found
     */
    protected function getDynamicRouteProcessor(string $route, string $requestMethod = '')
    {
        $bunches = $this->paramRoutes[$requestMethod == '' ? $_SERVER['REQUEST_METHOD'] : $requestMethod];

        foreach ($bunches as $bunch) {
            $matches = [];

            if (preg_match($bunch['regexp'], $route, $matches)) {
                $routeData = $bunch['bunch'][count($matches)];
                $regExPattern = $this->_getRouteMatcherRegExPattern($routeData['pattern']);

                // try match
                $values = [];
                if (preg_match('/^' . str_replace('/', '\\/', $regExPattern) . '$/', $route, $values)) {
                    // fetch parameter names
                    $names = $this->_getParameterNames($routeData['pattern']);

                    $this->parameters = [];
                    foreach ($names as $i => $name) {
                        $this->parameters[$name] = $values[$i + 1];
                    }

                    $this->calledRoute = $routeData['pattern'];

                    return $routeData['callback'];
                }
            }
        }

        // match was not found
        return false;
    }

    /**
     * Method searches dynamic route processor
     *
     * @param string $route
     *            Route
     * @return string|bool Result of the router'scall or false if any error occured
     */
    public function findDynamicRouteProcessor(string $route)
    {
        $processor = $this->getDynamicRouteProcessor($route);

        if ($processor === false) {
            return false;
        }

        return $this->executeHandler($processor, $route);
    }

    /**
     * Checking that method exists
     *
     * @param mixed $processor
     *            callback object
     * @param ?string $functionName
     *            callback method
     * @return bool true if method does not exists
     */
    private function methodDoesNotExists($processor, ?string $functionName): bool
    {
        return isset($processor[0]) && method_exists($processor[0], $functionName) === false;
    }

    /**
     * Checking that handler can be called
     *
     * @param object|array|callable $processor
     *            callback object
     * @param ?string $functionName
     *            callback method
     * @return bool
     */
    private function canBeCalled($processor, ?string $functionName): bool
    {
        return is_callable($processor) &&
            (method_exists($processor[0], $functionName) || isset($processor[0]->$functionName));
    }

    /**
     * Checking that processor can be called as function
     *
     * @param mixed $processor
     *            route processor
     * @return bool true if the $processor can be called as function
     */
    private function isFunction($processor): bool
    {
        return is_callable($processor) && is_array($processor) === false;
    }

    /**
     * Method returns either universal hanler if it fits or normal handler
     *
     * @param array $processors
     *            list of routes and handlers
     * @param string $route
     *            calling route
     * @return mixed processor
     */
    protected function getExactRouteHandlerOrUniversal(&$processors, string $route)
    {
        $this->calledRoute = $route;

        if ($this->universalRouteWasAdded) {
            $allRoutes = array_keys($processors);

            if (array_search('*', $allRoutes) <= array_search($route, $allRoutes)) {
                $processor = $processors['*'];
                $this->calledRoute = '*';
            } else {
                $processor = $processors[$route];
            }
        } else {
            $processor = $processors[$route];
        }

        return $processor;
    }

    /**
     * Method registeres middleware for the router
     *
     * @param callable $middleware
     *            middleware
     */
    public function registerMiddleware(string $router, callable $middleware): void
    {
        $this->middleware[trim($router, '/')] = $middleware;
    }

    /**
     * Method returns middleware processing result
     *
     * @param string $route
     *            processed route
     * @return array middleware result
     */
    private function getMiddlewareResult(string $route): array
    {
        return isset($this->middleware[$this->calledRoute]) ? call_user_func(
            $this->middleware[$this->calledRoute],
            $route,
            $this->parameters) : [
            $route,
            $this->parameters
        ];
    }

    /**
     * Method executes route handler
     *
     * @param mixed $processor
     * @param string $route
     * @return mixed route handler execution result
     */
    protected function executeHandler($processor, string $route)
    {
        if ($this->isFunction($processor)) {
            return call_user_func_array($processor, $this->getMiddlewareResult($route));
        }

        $functionName = $processor[1] ?? null;

        if ($this->canBeCalled($processor, $functionName)) {
            // passing route path and parameters
            return call_user_func_array($processor, $this->getMiddlewareResult($route));
        } else {
            $callableDescription = \Mezon\Router\Utils::getCallableDescription($processor);

            if ($this->methodDoesNotExists($processor, $functionName)) {
                throw (new \Exception("'$callableDescription' does not exists"));
            } else {
                throw (new \Exception("'$callableDescription' must be callable entity"));
            }
        }
    }

    /**
     * Method returns route handler
     *
     * @param string $route
     *            Route
     * @return array|callable|bool route handler
     */
    protected function getStaticRouteProcessor(string $route)
    {
        $processors = $this->staticRoutes[$_SERVER['REQUEST_METHOD']];

        if (isset($processors[$route])) {
            $processor = $this->getExactRouteHandlerOrUniversal($processors, $route);
        } elseif (isset($processors['*'])) {
            $processor = $processors['*'];
        } else {
            return false;
        }

        return $processor;
    }

    /**
     * Method searches route processor
     *
     * @param string $route
     *            Route
     * @return mixed Result of the router processor
     */
    public function findStaticRouteProcessor(string $route)
    {
        $processor = $this->getStaticRouteProcessor($route);

        if ($processor === false) {
            return false;
        }

        return $this->executeHandler($processor, $route);
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
            throw (new \Exception('Parameter ' . $name . ' was not found in route', - 1));
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
     * Getting route by name
     *
     * @param string $routeName
     *            route's name
     * @return string route
     */
    public abstract function getRouteByName(string $routeName): string;

    /**
     * Compiling route into URL
     *
     * @param string $routeName
     *            route name
     * @param array $parameters
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
