<?php
namespace Mezon\Router;

trait RoutesSet
{

    /**
     * List of static routes for all supported request methods
     *
     * @var array
     */
    protected $staticRoutes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'OPTION' => [],
        'PATCH' => []
    ];

    /**
     * List of non static routes
     *
     * @var array
     */
    protected $paramRoutes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'OPTION' => [],
        'PATCH' => []
    ];

    /**
     * Bunch size
     *
     * @var integer
     */
    private $bunchSize = 100;

    /**
     * Generating appendix for the RegExp
     *
     * @param int $i
     *            count of ()
     * @return string appendix
     */
    private function getRegExpAppendix(int $i): string
    {
        $return = '';

        for ($n = 0; $n <= $i; $n ++) {
            $return .= '()';
        }

        return $return;
    }

    /**
     * Method compiles regexp for the bunch of routes
     *
     * @param array $bunch
     */
    protected function compileRegexpForBunch(array &$bunch): void
    {
        if (! empty($bunch['bunch'])) {
            $bunch['regexp'] = '';
            $hashTable = [];
            $items = [];
            $previousIndex = 0;

            foreach ($bunch['bunch'] as $route) {
                $vars = $this->_getParameterNames($route['pattern']);
                $routeMatcher = $this->_getRouteMatcherRegExPattern($route['pattern']);
                $currentIndex = count($vars) + 1;
                if (isset($hashTable[$currentIndex])) {
                    $items[] = $routeMatcher . $this->getRegExpAppendix($previousIndex - $currentIndex);
                    $currentIndex = $previousIndex + 1;
                    $hashTable[$currentIndex] = $route;
                } else {
                    $items[] = $routeMatcher;
                    $hashTable[$currentIndex] = $route;
                }
                $previousIndex = $currentIndex;
            }

            $bunch['regexp'] = '~^(?|' . implode('|', $items) . ')$~';
            $bunch['bunch'] = $hashTable;
        }
    }

    /**
     * Were regexps compiled?
     */
    private $regExpsWereCompiled = false;

    /**
     * Method compiles all regeps for all routes
     */
    protected function compileRegexpForBunches(): void
    {
        if (! $this->regExpsWereCompiled) {
            foreach (self::getListOfSupportedRequestMethods() as $requestMethod) {
                foreach ($this->paramRoutes[$requestMethod] as &$bunch) {
                    $this->compileRegexpForBunch($bunch);
                }
            }

            $this->regExpsWereCompiled = true;
        }
    }

    /**
     * Method adds param router
     *
     * @param string $requestMethod
     * @param string $route
     * @param mixed $callback
     */
    protected function addParamRoute(string $requestMethod, string $route, $callback): void
    {
        if (empty($this->paramRoutes[$requestMethod])) {
            $this->paramRoutes[$requestMethod] = [
                [
                    'bunch' => []
                ]
            ];
        }

        $bunchCursor = count($this->paramRoutes[$requestMethod]) - 1;

        $lastBunchSize = count($this->paramRoutes[$requestMethod][$bunchCursor]['bunch']);

        if ($lastBunchSize == $this->bunchSize) {
            $this->paramRoutes[$requestMethod][] = [
                'bunch' => []
            ];
            $bunchCursor ++;
            $lastBunchSize = 0;
        }

        $this->paramRoutes[$requestMethod][$bunchCursor]['bunch'][$lastBunchSize + 1] = [
            'pattern' => $route,
            'callback' => $callback
        ];
    }

    /**
     * Route names
     *
     * @var array
     */
    private $routeNames = [];

    /**
     * Method validates request method
     *
     * @param string $requestMethod
     *            HTTP request method
     */
    protected function validateRequestMethod(string $requestMethod): void
    {
        if (isset($this->staticRoutes[$requestMethod]) === false) {
            throw (new \Exception('Unsupported request method'));
        }
    }

    /**
     * Method returns a list of supported request methods
     *
     * @return array list of supported request methods
     */
    public static function getListOfSupportedRequestMethods(): array
    {
        return [
            'GET',
            'POST',
            'PUT',
            'DELETE',
            'OPTION',
            'PATCH'
        ];
    }

    /**
     * Method clears router data.
     */
    public function clear()
    {
        $this->routeNames = [];

        foreach (self::getListOfSupportedRequestMethods() as $requestMethod) {
            $this->staticRoutes[$requestMethod] = [];
            $this->paramRoutes[$requestMethod] = [];
        }

        $this->cachedRegExps = [];

        $this->middleware = [];

        $this->regExpsWereCompiled = false;
    }

    /**
     * Method returns true if the param router exists
     *
     * @param string $route
     *            checking route
     * @param string $requestMethod
     *            HTTP request method
     * @return bool true if the param router exists, false otherwise
     */
    private function paramRouteExists(string $route, string $requestMethod): bool
    {
        foreach ($this->paramRoutes[$requestMethod] as $bunch) {
            foreach ($bunch['bunch'] as $item) {
                if ($item['pattern'] === $route) {
                    return true;
                }
            }
        }

        return false;
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
        $this->compileRegexpForBunches();

        $route = trim($route, '/');

        foreach (self::getListOfSupportedRequestMethods() as $requestMethod) {
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
     * Method rturns all available routes
     */
    public function getAllRoutesTrace()
    {
        $fullTrace = [];

        foreach (self::getListOfSupportedRequestMethods() as $requestMethod) {
            $trace = [
                $requestMethod . ' : '
            ];
            $hasRoutes = false;
            if (! empty($this->staticRoutes[$requestMethod])) {
                $trace[] = implode(', ', array_keys($this->staticRoutes[$requestMethod]));
                $trace[] = ', ';
                $hasRoutes = true;
            }
            if (! empty($this->paramRoutes[$requestMethod])) {
                foreach ($this->paramRoutes[$requestMethod] as $bunch) {
                    $items = [];
                    foreach ($bunch['bunch'] as $item) {
                        $items[] = $item['pattern'];
                        $hasRoutes = true;
                    }
                    $trace[] = implode(', ', $items);
                }
            }

            if (! $hasRoutes) {
                $trace[] = '<none>';
            }

            $fullTrace[] = implode('', $trace);
        }

        return implode('; ', $fullTrace);
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
            $this->validateRequestMethod($requestMethod);

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

    /**
     * Method dumps all routes and their names on disk
     *
     * @param string $filePath
     *            file path to cache
     * @codeCoverageIgnore
     */
    public function dumpOnDisk(string $filePath = './cache/cache.php'): void
    {
        $this->compileRegexpForBunches();

        file_put_contents($filePath, '<?php return ' . var_export([
            0 => $this->staticRoutes,
            1 => $this->paramRoutes,
            2 => $this->routeNames,
            3 => $this->cachedRegExps,
            4 => $this->cachedParameters,
            5 => $this->regExpsWereCompiled
        ], true) . ';');
    }

    /**
     * Method loads routes from disk
     *
     * @param string $filePath
     *            file path to cache
     * @codeCoverageIgnore
     */
    public function loadFromDisk(string $filePath = './cache/cache.php'): void
    {
        list ($this->staticRoutes, $this->paramRoutes, $this->routeNames, $this->cachedRegExps, $this->cachedParameters, $this->regExpsWereCompiled) = require ($filePath);
    }
}
