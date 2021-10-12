<?php
namespace Mezon\Router;

use Mezon\Router\Types\BaseType;

trait SimpleUrlParser
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
    private function _getRouteMatcherRegExPattern(string $routerPattern): string
    {
        // parsing routes
        $compiledRouterPattern = $routerPattern;

        foreach ($this->types as $typeClass) {
            $compiledRouterPattern = preg_replace(
                '/' . $typeClass::searchRegExp() . '/',
                '(' . $typeClass::parserRegExp() . ')',
                $compiledRouterPattern);
        }

        return str_replace('/', '\/', $compiledRouterPattern);
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

        return $return;
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
        $routes = $this->paramRoutes[$requestMethod == '' ? $_SERVER['REQUEST_METHOD'] : $requestMethod];

        foreach ($routes as $item) {
            $matches = [];

            if (preg_match('/^'.$this->_getRouteMatcherRegExPattern($item['pattern']).'$/', $route, $matches)) {
                $names = $this->_getParameterNames($item['pattern']);

                $this->parameters = [];
                foreach ($names as $i => $name) {
                    $this->parameters[$name] = $matches[(int) $i + 1];
                }

                $this->calledRoute = $item['pattern'];

                return $item['callback'];
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
        return $functionName === null || (isset($processor[0]) && method_exists($processor[0], $functionName) === false);
    }

    /**
     * Checking that handler can be called
     *
     * @param object|array|callable $processor
     *            callback object
     * @param ?string $functionName
     *            callback method
     * @return bool
     * @psalm-suppress InvalidArrayAccess
     */
    private function canBeCalled($processor, ?string $functionName): bool
    {
        return is_callable($processor) &&
            ($functionName !== null &&
            (method_exists($processor[0], $functionName) || isset($processor[0]->$functionName)));
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
     * Method registeres middleware for the router
     *
     * @param callable $middleware
     *            middleware
     */
    public function registerMiddleware(string $router, callable $middleware): void
    {
        $routerTrimmed = trim($router, '/');

        if (! isset($this->middleware[$routerTrimmed])) {
            $this->middleware[$routerTrimmed] = [];
        }

        $this->middleware[$routerTrimmed][] = $middleware;
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
        $middleWares = [];

        if (isset($this->middleware['*'])) {
            $middleWares = $this->middleware['*'];
        }

        if ($this->calledRoute !== '*' && isset($this->middleware[$this->calledRoute])) {
            $middleWares = array_merge($middleWares, $this->middleware[$this->calledRoute]);
        }

        $result = [
            $route,
            $this->parameters
        ];

        if (! count($middleWares)) {
            return $result;
        }

        foreach ($middleWares as $middleWare) {
            $result = call_user_func($middleWare, $route, $this->parameters);

            if (is_array($result)) {
                if (array_key_exists(0, $result)) {
                    $route = $result[0];
                }

                if (array_key_exists(1, $result)) {
                    $this->parameters = $result[1];
                }
            }
        }

        return [
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
            $callableDescription = Utils::getCallableDescription($processor);

            if ($this->methodDoesNotExists($processor, $functionName)) {
                throw (new \Exception("'$callableDescription' does not exists"));
            } else {
                // @codeCoverageIgnoreStart
                throw (new \Exception("'$callableDescription' must be callable entity"));
                // @codeCoverageIgnoreEnd
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
            $this->calledRoute = $route;

            return $processors[$route];
        } else {
            return false;
        }
    }

    /**
     * Method returns route handler
     *
     * @return array|callable|bool route handler
     */
    protected function getUniversalRouteProcessor()
    {
        $processors = $this->staticRoutes[$_SERVER['REQUEST_METHOD']];

        if (isset($processors['*'])) {
            $this->calledRoute = '*';

            return $processors['*'];
        } else {
            return false;
        }
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
     * Method searches universal route processor
     *
     * @param string $route
     *            Route
     * @return mixed Result of the router processor
     */
    public function findUniversalRouteProcessor(string $route)
    {
        $processor = $this->getUniversalRouteProcessor();

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
