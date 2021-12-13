<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Trait UrlParserBase
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2021/09/27)
 * @copyright Copyright (c) 2021, aeon.org
 */
trait UrlParserBase
{

    /**
     * Middleware for routes processing
     *
     * @var array<string, callable[]>
     */
    protected $middleware = [];

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
     * Parsed parameters of the calling router
     *
     * @var string[]
     */
    protected $parameters = [];

    /**
     * Method returns route parameter
     *
     * @param string $name
     *            route parameter
     * @return string route parameter
     */
    public function getParam(string $name): string
    {
        if (! isset($this->parameters[$name])) {
            throw (new \Exception('Parameter ' . $name . ' was not found in route', - 1));
        }

        return $this->parameters[$name];
    }

    /**
     * Does parameter exists
     *
     * @param string $name
     *            param name
     * @return bool true if the parameter exists
     */
    public function hasParam(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Method executes route handler
     *
     * @param
     *            callable|string|array{0: string, 1: string} $processor processor
     * @psalm-param callable|string|array{0: string, 1: string} $processor
     * @param string $route
     *            route
     * @return mixed route handler execution result
     */
    protected function executeHandler($processor, string $route)
    {
        if (is_callable($processor)) {
            return call_user_func_array($processor, $this->getMiddlewareResult($route));
        }

        $functionName = $processor[1] ?? null;

        $callableDescription = Utils::getCallableDescription($processor);

        if ($this->methodDoesNotExists($processor, $functionName)) {
            throw (new \Exception("'$callableDescription' does not exists"));
        } else {
            // @codeCoverageIgnoreStart
            throw (new \Exception("'$callableDescription' must be callable entity"));
            // @codeCoverageIgnoreEnd
        }
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

        /** @var callable $middleWare */
        foreach ($middleWares as $middleWare) {
            /** @var array{0: string, 1: string[]}|null $result */
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
     * Method searches dynamic route processor
     *
     * @param string $route
     *            route
     * @param string $requestMethod
     *            request method
     * @return array{0: string, 1:string}|callable|string|false route's handler or false in case the handler was not found
     */
    abstract protected function getDynamicRouteProcessor(string $route, string $requestMethod = '');

    /**
     * Method searches dynamic route processor
     *
     * @param string $route
     *            route
     * @return mixed|false result of the router's call or false if any error occured
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
        return $functionName === null || (isset($processor[0]) && is_object($processor[0]) && method_exists($processor[0], $functionName) === false);
    }

    /**
     * Method returns request method
     *
     * @return string request method
     */
    private function getRequestMethod(): string
    {
        return isset($_SERVER['REQUEST_METHOD']) ? (string) $_SERVER['REQUEST_METHOD'] : 'GET';
    }
}
