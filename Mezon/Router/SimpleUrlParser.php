<?php
declare(strict_types = 1);
namespace Mezon\Router;

use Mezon\Router\Types\BaseType;

/**
 * Trait SimpleUrlParser
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2021/09/27)
 * @copyright Copyright (c) 2021, http://aeon.su
 */
trait SimpleUrlParser
{

    use UrlParserBase;

    /**
     * Method compiles route pattern string in regex string.
     * For example [i:id]/some-str in ([\[0-9\]])/some-str
     *
     * @param string $routerPattern
     *            router pattern
     * @return string regexp pattern
     */
    private function _getRouteMatcherRegExPattern(string $routerPattern): string
    {
        // parsing routes
        $compiledRouterPattern = $routerPattern;

        foreach ($this->types as $typeClass) {
            $compiledRouterPattern = preg_replace(
                '/' . $typeClass::searchRegExp() . '/',
                '(' . $typeClass::parserRegExp() . ')',
                $compiledRouterPattern);
        }

        return str_replace('/', '\/', $compiledRouterPattern);
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

        return $return;
    }

    /**
     * Method searches dynamic route processor
     *
     * @param string $route
     *            route
     * @param string $requestMethod
     *            request method
     * @return array{0: string, 1:string}|callable|string|false route's handler or false in case the handler was not found
     * @psalm-suppress PossiblyUndefinedArrayOffset
     */
    protected function getDynamicRouteProcessor(string $route, string $requestMethod = '')
    {
        $routes = $this->paramRoutes[$requestMethod == '' ? $this->getRequestMethod() : $requestMethod];

        foreach ($routes as $item) {
            $matches = [];

            if (preg_match('/^' . $this->_getRouteMatcherRegExPattern($item['pattern']) . '$/u', $route, $matches)) {
                $names = $this->_getParameterNames($item['pattern']);

                $this->parameters = [];
                foreach ($names as $i => $name) {
                    $this->parameters[$name] = $matches[(int) $i + 1];
                }

                $this->setCalledRoute($item['pattern']);

                return $item['callback'];
            }
        }

        // match was not found
        return false;
    }
}
