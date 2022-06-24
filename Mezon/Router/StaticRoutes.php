<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Trait StaticRoutes
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2021/09/27)
 * @copyright Copyright (c) 2021, http://aeon.su
 */
trait StaticRoutes
{

    /**
     * List of static routes for all supported request methods
     *
     * @var array<string, array<string, array{0: string, 1:string}|callable|string>>
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
     * Method sets $calledRoute
     *
     * @param string $calledRoute
     *            called router
     */
    protected abstract function setCalledRoute(string $calledRoute): void;

    /**
     * Method returns static routes handlers for the specified request methods
     *
     * @param string $requestMethod
     *            request method
     * @return array<string, array{0: string, 1:string}|callable|string> handlers
     */
    protected function getStaticRoutes(string $requestMethod): array
    {
        $requestMethod = (string) $_SERVER['REQUEST_METHOD'];

        SupportedRequestMethods::validateRequestMethod($requestMethod);

        return $this->staticRoutes[$requestMethod];
    }

    /**
     * Method returns route handler
     *
     * @param string $route
     *            routes
     * @return array{0: string, 1: string}|callable|false|string route handler
     */
    protected function getStaticRouteProcessor(string $route)
    {
        $processors = $this->getStaticRoutes((string) $_SERVER['REQUEST_METHOD']);

        if (isset($processors[$route])) {
            $this->setCalledRoute($route);

            return $processors[$route];
        } else {
            return false;
        }
    }

    /**
     * Method returns route handler
     *
     * @return array{0: string, 1: string}|callable|false|string route handler
     */
    protected function getUniversalRouteProcessor()
    {
        $processors = $this->getStaticRoutes((string) $_SERVER['REQUEST_METHOD']);

        if (isset($processors['*'])) {
            $this->setCalledRoute('*');

            return $processors['*'];
        } else {
            return false;
        }
    }
}
