<?php
namespace Mezon\Router;

trait UrlParser
{

    /**
     * Parsed parameters of the calling router
     *
     * @var array
     */
    protected $parameters = [];

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

        if (isset($this->types[$parameterData[0]])) {
            if ($this->types[$parameterData[0]]($component)) {
                return $parameterData[1];
            } else {
                return '';
            }
        } else {
            throw (new \Exception('Unknown parameter type : ' . $parameterData[0]));
        }
    }

    /**
     * Method matches route and pattern
     *
     * @param array $cleanRoute
     *            Cleaned route splitted in parts
     * @param array $cleanPattern
     *            Route pattern
     * @return array|bool Array of route's parameters
     */
    private function _matchRouteAndPattern(array $cleanRoute, array $cleanPattern)
    {
        if (count($cleanRoute) !== count($cleanPattern)) {
            return false;
        }

        $paremeters = [];
        $patternsCount = count($cleanPattern);

        for ($i = 0; $i < $patternsCount; $i ++) {
            if (\Mezon\Router\Utils::isParameter($cleanPattern[$i])) {
                $parameterName = $this->_matchParameterAndComponent($cleanRoute[$i], $cleanPattern[$i]);

                // it's a parameter
                if ($parameterName !== '') {
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
     * @return array|callable|bool route's handler or false in case the handler was not found
     */
    protected function getDynamicRouteProcessor(array &$processors, string $route)
    {
        $cleanRoute = explode('/', trim($route, '/'));

        foreach ($processors as $i => $processor) {
            $cleanPattern = explode('/', trim($i, '/'));

            if ($this->_matchRouteAndPattern($cleanRoute, $cleanPattern) !== false) {
                return $processor;
            }
        }

        return false;
    }

    /**
     * Method searches dynamic route processor
     *
     * @param array $processors
     *            Callable router's processor
     * @param string $route
     *            Route
     * @return string|bool Result of the router'scall or false if any error occured
     */
    public function findDynamicRouteProcessor(array &$processors, string $route)
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
        if ($this->universalRouteWasAdded) {
            $allRoutes = array_keys($processors);

            if (array_search('*', $allRoutes) <= array_search($route, $allRoutes)) {
                $processor = $processors['*'];
            } else {
                $processor = $processors[$route];
            }
        } else {
            $processor = $processors[$route];
        }

        return $processor;
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
            return $processor($route, []);
        }

        $functionName = $processor[1] ?? null;

        if ($this->canBeCalled($processor, $functionName)) {
            // passing route path and parameters
            return call_user_func($processor, $route, []);
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
     * @param mixed $processors
     *            Callable router's processor
     * @param string $route
     *            Route
     * @return array|callable|bool route handler
     */
    protected function getStaticRouteProcessor(&$processors, string $route)
    {
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
     * @param mixed $processors
     *            Callable router's processor
     * @param string $route
     *            Route
     * @return mixed Result of the router processor
     */
    public function findStaticRouteProcessor(&$processors, string $route)
    {
        $processor = $this->getStaticRouteProcessor($processors, $route);

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
}