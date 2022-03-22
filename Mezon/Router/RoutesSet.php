<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Trait RouterSet
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/15)
 * @copyright Copyright (c) 2019, http://aeon.su
 */
trait RoutesSet
{

    use StaticRoutes, ParamRoutes, RoutesSetBase;

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
    private function compileRegexpForBunch(array &$bunch): void
    {
        if (! empty($bunch['bunch'])) {
            $bunch['regexp'] = '';
            /** @var array<int, array{pattern: string}> $hashTable */
            $hashTable = [];
            $items = [];

            /** @var array{pattern: string} $route */
            foreach ($bunch['bunch'] as $route) {
                $vars = $this->_getParameterNames($route['pattern']);
                $routeMatcher = $this->_getRouteMatcherRegExPattern($route['pattern']);
                $currentIndex = count($vars) + 1;
                if (isset($hashTable[$currentIndex])) {
                    $maxIndex = max(array_keys($hashTable));
                    $items[] = $routeMatcher . $this->getRegExpAppendix($maxIndex - $currentIndex);
                    $currentIndex = $maxIndex + 1;
                    $hashTable[$currentIndex] = $route;
                } else {
                    $items[] = $routeMatcher;
                    $hashTable[$currentIndex] = $route;
                }
            }

            $bunch['regexp'] = '~^(?|' . implode('|', $items) . ')$~';
            $bunch['bunch'] = $hashTable;
        }
    }

    /**
     * Were regexps compiled?
     *
     * @var bool
     */
    private $regExpsWereCompiled = false;

    /**
     * Method compiles all regeps for all routes
     */
    private function compileRegexpForBunches(): void
    {
        if (! $this->regExpsWereCompiled) {
            foreach (SuppportedRequestMethods::getListOfSupportedRequestMethods() as $requestMethod) {
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
     *            request method
     * @param string $route
     *            route
     * @param array{0: string, 1: string}|callable|string $callback
     *            callback method
     * @psalm-suppress PossiblyUndefinedArrayOffset
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
     * Method clears other data
     */
    protected function clearOtherData(): void
    {
        $this->cachedRegExps = [];

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
    protected function paramRouteExists(string $route, string $requestMethod): bool
    {
        /** @var array{bunch: array} $bunch */
        foreach ($this->paramRoutes[$requestMethod] as $bunch) {
            /** @var array{pattern: string} $item */
            foreach ($bunch['bunch'] as $item) {
                if ($item['pattern'] === $route) {
                    return true;
                }
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
     * @psalm-suppress UnresolvableInclude, MixedArrayAccess, MixedAssignment
     */
    public function loadFromDisk(string $filePath = './cache/cache.php'): void
    {
        list ($this->staticRoutes, $this->paramRoutes, $this->routeNames, $this->cachedRegExps, $this->cachedParameters, $this->regExpsWereCompiled) = require ($filePath);
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
            // TODO try to remove $hasRoutes flag
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
}
