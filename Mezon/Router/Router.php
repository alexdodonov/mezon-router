<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Class Router
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/15)
 * @copyright Copyright (c) 2019, http://aeon.su
 */

/**
 * Router class
 */
class Router extends RouterBase implements RouterInterface
{

    use RoutesSet, UrlParser;

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

        return parent::callRoute($route);
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

        return parent::getCallback($route);
    }
}
