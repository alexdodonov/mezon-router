<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Class Router
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/15)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Router class
 */
class Router implements RouterInterface
{

    use RoutesSet, UrlParser, RouteTypes, InvalidRouteErrorHandler;

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
        if (! $this->regExpsWereCompiled) {
            $this->compileRegexpForBunches();
        }

        $route = Utils::prepareRoute($route);
        $requestMethod = $this->getRequestMethod();
        SuppportedRequestMethods::validateRequestMethod($requestMethod);

        if (($result = $this->findStaticRouteProcessor($route)) !== false) {
            return $result;
        }
        if (($result = $this->findDynamicRouteProcessor($route)) !== false) {
            return $result;
        }
        if (($result = $this->findUniversalRouteProcessor($route)) !== false) {
            return $result;
        }
        call_user_func($this->getNoProcessorErrorHandler(), $route);
    }

    /**
     * Method returns call back by it's router
     *
     * @param string[]|string $route
     *            route
     * @return array{0: string, 1: string}|callable|string|false route callback
     * @psalm-suppress MixedAssignment
     */
    public function getCallback($route)
    {
        $this->compileRegexpForBunches();

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
    } // @codeCoverageIgnoreEnd
}
