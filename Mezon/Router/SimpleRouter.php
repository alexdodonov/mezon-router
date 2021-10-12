<?php
namespace Mezon\Router;

/**
 * Class SimpleRouter
 *
 * @package Mezon
 * @subpackage Router
 * @author Dodonov A.A.
 * @version v.1.0 (2021/09/27)
 * @copyright Copyright (c) 2021, aeon.org
 */

/**
 * Simple router class
 */
class SimpleRouter implements RouterInterface
{

    use SimpleRoutesSet, SimpleUrlParser, RouteTypes;

    /**
     * Method wich handles invalid route error
     *
     * @var callable
     */
    private $invalidRouteErrorHandler;

    /**
     * Method returns request method
     *
     * @return string Request method
     */
    private function getRequestMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $_SERVER['REQUEST_METHOD'] = $this->getRequestMethod();

        $this->invalidRouteErrorHandler = [
            $this,
            'noProcessorFoundErrorHandler'
        ];

        $this->initDefaultTypes();
    }

    /**
     * Method fetches actions from the objects and creates GetRoutes for them
     *
     * @param object $object
     *            Object to be processed
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
     * Method processes no processor found error
     *
     * @param string $route
     *            Route
     */
    public function noProcessorFoundErrorHandler(string $route): void
    {
        throw (new \Exception(
            'The processor was not found for the route ' . $route . ' in ' . $this->getAllRoutesTrace()));
    }

    /**
     * Method sets InvalidRouteErrorHandler function
     *
     * @param callable $function
     *            Error handler
     *            
     * @return callable old error handler
     */
    public function setNoProcessorFoundErrorHandler(callable $function): callable
    {
        $oldErrorHandler = $this->invalidRouteErrorHandler;

        $this->invalidRouteErrorHandler = $function;

        return $oldErrorHandler;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Mezon\Router\RouterInterface::callRoute()
     */
    public function callRoute($route)
    {
        $route = Utils::prepareRoute($route);
        $requestMethod = $this->getRequestMethod();
        $this->validateRequestMethod($requestMethod);

        if (($result = $this->findStaticRouteProcessor($route)) !== false) {
            return $result;
        }
        if (($result = $this->findDynamicRouteProcessor($route)) !== false) {
            return $result;
        }
        if (($result = $this->findUniversalRouteProcessor($route)) !== false) {
            return $result;
        }
        call_user_func($this->invalidRouteErrorHandler, $route);
    }

    /**
     * Method returns call back by it's router
     *
     * @param array|string $route
     *            route
     * @return array|callable|bool route callback
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

        call_user_func($this->invalidRouteErrorHandler, $route); // @codeCoverageIgnoreStart
        return false;
    } // @codeCoverageIgnoreEnd
}
