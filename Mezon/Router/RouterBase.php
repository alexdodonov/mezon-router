<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Class RouterBase
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2022/06/22)
 * @copyright Copyright (c) 2022, http://aeon.su
 */

/**
 * Base router class
 */
abstract class RouterBase implements RouterInterface
{

    use RouteTypes, InvalidRouteErrorHandler, StaticRoutes;

    /**
     * Called route
     *
     * @var string
     */
    private $calledRoute = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $_SERVER['REQUEST_METHOD'] = $this->getRequestMethod();

        $this->initDefaultTypes();
    }

    /**
     * Method fetches actions from the objects and creates GetRoutes for them
     *
     * @param object $object
     *            object to be processed
     * @param array $map
     *            map
     */
    public function fetchActions(object $object, array $map = []): void
    {
        $methods = get_class_methods($object);

        foreach ($methods as $method) {
            if (strpos($method, 'action') === 0) {
                $route = Utils::convertMethodNameToRoute($method);

                $key = str_replace('action', '', $method);
                /** @var string[] $requestMethods */
                $requestMethods = array_key_exists($key, $map) ? $map[$key] : [
                    'GET',
                    'POST'
                ];

                $this->addRoute($route, [
                    $object,
                    $method
                ], $requestMethods);
            }
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see RouterInterface::callRoute()
     * @psalm-suppress MixedAssignment
     */
    public function callRoute($route)
    {
        $route = Utils::prepareRoute($route);
        $requestMethod = $this->getRequestMethod();
        SupportedRequestMethods::validateRequestMethod($requestMethod);

        if (($processor = $this->getStaticRouteProcessor($route)) === false) {
            if (($processor = $this->getDynamicRouteProcessor($route)) === false) {
                $processor = $this->getUniversalRouteProcessor();
            }
        }

        if ($processor === false) {
            call_user_func($this->getNoProcessorErrorHandler(), $route);
        } else {
            return $this->executeHandler($processor, $route);
        }
    }

    /**
     * Method returns call back by it's router
     *
     * @param string[]|string $route
     *            route
     * @return array{0: string, 1: string}|callable|false|string route callback
     * @psalm-suppress MixedReturnStatement
     */
    public function getCallback($route)
    {
        $route = Utils::prepareRoute($route);

        if (($result = $this->getStaticRouteProcessor($route)) !== false) {
            return $result;
        }

        if (($result = $this->getDynamicRouteProcessor($route)) !== false) {
            return $result;
        }

        if (($result = $this->getUniversalRouteProcessor()) !== false) {
            return $result;
        }

        call_user_func($this->getNoProcessorErrorHandler(), $route); // @codeCoverageIgnoreStart
        return false;
    }

    // @codeCoverageIgnoreEnd

    /**
     * Method returns request method
     *
     * @return string request method
     */
    protected function getRequestMethod(): string
    {
        return isset($_SERVER['REQUEST_METHOD']) ? (string) $_SERVER['REQUEST_METHOD'] : 'GET';
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
    protected abstract function getDynamicRouteProcessor(string $route, string $requestMethod = '');

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
    protected abstract function executeHandler($processor, string $route);

    /**
     * Middleware for routes processing
     *
     * @var array<string, callable[]>
     */
    protected $middleware = [];

    /**
     * Method clears middleware
     */
    protected function clearMiddleware(): void
    {
        $this->middleware = [];
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
    protected function getMiddlewareResult(string $route): array
    {
        $middleWares = [];

        if (isset($this->middleware['*'])) {
            $middleWares = $this->middleware['*'];
        }

        if ($this->getCalledRoute() !== '*' && isset($this->middleware[$this->getCalledRoute()])) {
            $middleWares = array_merge($middleWares, $this->middleware[$this->getCalledRoute()]);
        }

        if (empty($middleWares)) {
            return [
                $route,
                $this->getParameters()
            ];
        }

        $this->setParameters(array_merge([
            $route
        ], $this->getParameters()));

        /** @var callable $middleWare */
        foreach ($middleWares as $middleWare) {
            /** @var array|null|mixed $result */
            $result = call_user_func_array($middleWare, $this->getParameters());

            if (is_array($result)) {
                $this->setParameters($result);
            } elseif ($result !== null) {
                $this->setParameters([
                    $result
                ]);
            }
        }

        return $this->getParameters();
    }

    /**
     * Method sets $calledRoute
     *
     * @param string $calledRoute
     *            called router
     */
    protected function setCalledRoute(string $calledRoute): void
    {
        $this->calledRoute = $calledRoute;
    }

    /**
     * Method returns called route
     *
     * @return string called route
     */
    public function getCalledRoute(): string
    {
        return $this->calledRoute;
    }

    /**
     * Method sets parameters
     *
     * @param mixed[] $parameters
     *            parameters
     */
    protected abstract function setParameters(array $parameters): void;

    /**
     * Method returns parameters
     *
     * @return mixed[]
     */
    protected abstract function getParameters(): array;
}
