<?php
namespace Mezon\Router;

class UrlParser
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
        $return = '';

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
        foreach ($processors as $i => $processor) {
            // exact router or 'all router'
            if ($i == $route || $i == '/*/') {
                if (is_callable($processor) && is_array($processor) === false) {
                    return $processor($route, []);
                }

                $functionName = $processor[1] ?? null;

                if (is_callable($processor) &&
                    (method_exists($processor[0], $functionName) || isset($processor[0]->$functionName))) {
                        // passing route path and parameters
                        return call_user_func($processor, $route, []);
                    } else {
                        $callableDescription = \Mezon\Router\Utils::getCallableDescription($processor);

                        if (isset($processor[0]) && method_exists($processor[0], $functionName) === false) {
                            throw (new \Exception("'$callableDescription' does not exists"));
                        } else {
                            throw (new \Exception("'$callableDescription' must be callable entity"));
                        }
                    }
            }
        }

        return false;
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