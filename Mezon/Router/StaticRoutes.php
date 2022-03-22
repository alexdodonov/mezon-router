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
     * Method returns static routes handlers for the specified request methods
     *
     * @param string $requestMethod
     *            request method
     * @return array<string, array{0: string, 1:string}|callable|string> handlers
     */
    protected function getStaticRoutes(string $requestMethod): array
    {
        $requestMethod = (string) $_SERVER['REQUEST_METHOD'];

        if (! isset($this->staticRoutes[$requestMethod])) {
            throw (new \Exception('Unsupported request method : ' . $requestMethod, - 1));
        }

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
            $this->calledRoute = $route;

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
     *            route
     * @return mixed|false result of the router processor
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
     *            route
     * @return mixed|false result of the router processor
     */
    public function findUniversalRouteProcessor(string $route)
    {
        $processor = $this->getUniversalRouteProcessor();

        if ($processor === false) {
            return false;
        }

        return $this->executeHandler($processor, $route);
    }
}
