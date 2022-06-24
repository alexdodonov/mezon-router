<?php
declare(strict_types = 1);
namespace Mezon\Router;

use Mezon\Router\Types\BaseType;

/**
 * Trait UrlParser
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2021/09/27)
 * @copyright Copyright (c) 2021, http://aeon.su
 */
trait UrlParser
{

    use UrlParserBase;

    /**
     * Cache for regular expressions
     *
     * @var string[]
     */
    private $cachedRegExps = [];

    /**
     * Cached parameters for route
     *
     * @var array<string, string[]>
     */
    private $cachedParameters = [];

    /**
     * Method compiles route pattern string in regex string.
     * For example [i:id]/some-str in ([\[0-9\]])/some-str
     *
     * @param string $routerPattern
     *            router pattern
     * @return string regexp pattern
     * @psalm-suppress MixedMethodCall
     */
    private function _getRouteMatcherRegExPattern(string $routerPattern): string
    {
        $key = $routerPattern;

        // try read from cache
        if (isset($this->cachedRegExps[$key])) {
            return $this->cachedRegExps[$key];
        }

        // parsing routes
        $compiledRouterPattern = $routerPattern;
        foreach ($this->types as $typeClass) {
            $compiledRouterPattern = preg_replace(
                '/' . $typeClass::searchRegExp() . '/',
                '(' . $typeClass::parserRegExp() . ')',
                $compiledRouterPattern);
        }

        // final setup + save in cache
        $this->cachedRegExps[$key] = $compiledRouterPattern;

        return $compiledRouterPattern;
    }

    /**
     * Method returns all parameter names in the route
     *
     * @param string $routerPattern
     *            route
     * @return string[] names
     */
    private function _getParameterNames(string $routerPattern): array
    {
        if (isset($this->cachedParameters[$routerPattern])) {
            return $this->cachedParameters[$routerPattern];
        }

        $regExPattern = [];

        foreach (array_keys($this->types) as $typeName) {
            $regExPattern[] = $typeName;
        }

        $regExPattern = '\[(' . implode('|', $regExPattern) . '):(' . BaseType::PARAMETER_NAME_REGEXP . ')\]';

        $names = [];
        preg_match_all('/' . str_replace('/', '\\/', $regExPattern) . '/', $routerPattern, $names);

        $return = [];

        foreach ($names[2] as $name) {
            $return[] = $name;
        }

        $this->cachedParameters[$routerPattern] = $return;

        return $return;
    }

    /**
     * Method warms cache
     */
    public function warmCache(): void
    {
        foreach (SupportedRequestMethods::getListOfSupportedRequestMethods() as $requestMethod) {
            /** @var array{bunch: array} $bunch */
            foreach ($this->paramRoutes[$requestMethod] as $bunch) {
                /** @var array{pattern: string} $route */
                foreach ($bunch['bunch'] as $route) {
                    $this->_getRouteMatcherRegExPattern($route['pattern']);

                    $this->_getParameterNames($route['pattern']);
                }
            }
        }

        $this->compileRegexpForBunches();
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
    protected function getDynamicRouteProcessor(string $route, string $requestMethod = '')
    {
        $bunches = $this->paramRoutes[$requestMethod == '' ? $this->getRequestMethod() : $requestMethod];

        /** @var array{bunch: string[], regexp: string} $bunch */
        foreach ($bunches as $bunch) {
            $matches = [];

            if (preg_match($bunch['regexp'], $route, $matches)) {
                /** @var array{pattern: string, callback: callable} $routeData */
                $routeData = $bunch['bunch'][count($matches)];

                $names = $this->_getParameterNames($routeData['pattern']);

                $this->parameters = [];
                foreach ($names as $i => $name) {
                    /** @var string[] $matches */
                    $this->parameters[$name] = $matches[(int) $i + 1];
                }

                $this->setCalledRoute($routeData['pattern']);

                return $routeData['callback'];
            }
        }

        // match was not found
        return false;
    }
}
