<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Trait UrlParserBase
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2021/09/27)
 * @copyright Copyright (c) 2021, http://aeon.su
 */
trait UrlParserBase
{

    /**
     * Parsed parameters of the calling router
     *
     * @var mixed[]
     */
    protected $parameters = [];

    /**
     * Method returns parameters
     *
     * @return mixed[]
     */
    protected function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Method sets parameters
     *
     * @param mixed[] $parameters
     *            parameters
     */
    protected function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Method returns route parameter
     *
     * @param string $name
     *            route parameter
     * @return mixed route parameter
     */
    public function getParam(string $name)
    {
        if (! isset($this->getParameters()[$name])) {
            throw (new \Exception('Parameter ' . $name . ' was not found in route', - 1));
        }

        return $this->getParameters()[$name];
    }

    /**
     * Does parameter exists
     *
     * @param string $name
     *            param name
     * @return bool true if the parameter exists
     */
    public function hasParam(string $name): bool
    {
        return isset($this->getParameters()[$name]);
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
    abstract protected function getDynamicRouteProcessor(string $route, string $requestMethod = '');

    /**
     * Checking that method exists
     *
     * @param mixed $processor
     *            callback object
     * @param ?string $functionName
     *            callback method
     * @return bool true if method does not exists
     */
    private function methodDoesNotExists($processor, ?string $functionName): bool
    {
        return $functionName === null ||
            (isset($processor[0]) && is_object($processor[0]) && method_exists($processor[0], $functionName) === false);
    }

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
    protected function executeHandler($processor, string $route)
    {
        if (is_callable($processor)) {
            return call_user_func_array($processor, $this->getMiddlewareResult($route));
        }

        $functionName = $processor[1] ?? null;

        $callableDescription = Utils::getCallableDescription($processor);

        if ($this->methodDoesNotExists($processor, $functionName)) {
            throw (new \Exception("'$callableDescription' does not exists"));
        } else {
            // @codeCoverageIgnoreStart
            throw (new \Exception("'$callableDescription' must be callable entity"));
            // @codeCoverageIgnoreEnd
        }
    }
}
