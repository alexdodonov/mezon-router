<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Trait SimpleRoutesSet
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2021/09/27)
 * @copyright Copyright (c) 2021, aeon.org
 */
trait SimpleRoutesSet
{

    use StaticRoutes, ParamRoutes, RoutesSetBase;

    /**
     * Method adds param router
     *
     * @param string $requestMethod
     *            request method
     * @param string $route
     *            route
     * @param array{0: string, 1:string}|callable|string $callback
     *            callback method
     */
    protected function addParamRoute(string $requestMethod, string $route, $callback): void
    {
        if (empty($this->paramRoutes[$requestMethod])) {
            $this->paramRoutes[$requestMethod] = [];
        }

        $this->paramRoutes[$requestMethod][] = [
            'pattern' => $route,
            'callback' => $callback
        ];
    }

    /**
     * Method returns true if the param router exists
     *
     * @param string $route
     *            checking route
     * @param string $requestMethod
     *            HTTP request method
     * @return bool true if the param router exists, false otherwise
     * @psalm-suppress PossiblyUndefinedArrayOffset
     */
    protected function paramRouteExists(string $route, string $requestMethod): bool
    {
        foreach ($this->paramRoutes[$requestMethod] as $item) {
            if ($item['pattern'] === $route) {
                return true;
            }
        }

        return false;
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
        file_put_contents($filePath, '<?php return ' . var_export([
            0 => $this->staticRoutes,
            1 => $this->paramRoutes,
            2 => $this->routeNames
        ], true) . ';');
    }

    /**
     * Method loads routes from disk
     *
     * @param string $filePath
     *            file path to cache
     * @codeCoverageIgnore
     * @psalm-suppress UnresolvableInclude, MixedArrayAccess, MixedAssignment
     */
    public function loadFromDisk(string $filePath = './cache/cache.php'): void
    {
        list ($this->staticRoutes, $this->paramRoutes, $this->routeNames) = require ($filePath);
    }

    /**
     * Method rturns all available routes
     *
     * @return string trace
     * @psalm-suppress PossiblyUndefinedArrayOffset
     */
    public function getAllRoutesTrace(): string
    {
        $fullTrace = [];

        foreach (SuppportedRequestMethods::getListOfSupportedRequestMethods() as $requestMethod) {
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
                $items = [];
                foreach ($this->paramRoutes[$requestMethod] as $item) {
                    $items[] = $item['pattern'];
                    $hasRoutes = true;
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
}
