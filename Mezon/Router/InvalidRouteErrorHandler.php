<?php
declare(strict_types = 1);
namespace Mezon\Router;

/**
 * Trait InvalidRouteErrorHandler
 *
 * @package Router
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/15)
 * @copyright Copyright (c) 2019, http://aeon.su
 */

/**
 * Error handler for unexisting route
 *
 * @author gdever
 */
trait InvalidRouteErrorHandler
{

    /**
     * Method wich handles invalid route error
     *
     * @var ?callable
     */
    private $invalidRouteErrorHandler = null;

    /**
     * Method processes no processor found error
     *
     * @param string $route
     *            route
     */
    public function noProcessorFoundErrorHandler(string $route): void
    {
        throw (new \Exception('The processor was not found for the route ' . $route . ' in ' . $this->getAllRoutesTrace(), -1));
    }

    /**
     * Method sets InvalidRouteErrorHandler function
     *
     * @param callable $function
     *            error handler
     *            
     * @return ?callable old error handler
     */
    public function setNoProcessorFoundErrorHandler(callable $function): ?callable
    {
        $oldErrorHandler = $this->invalidRouteErrorHandler;

        $this->invalidRouteErrorHandler = $function;

        return $oldErrorHandler;
    }

    /**
     * Method returns error handler
     *
     * @return callable
     */
    public function getNoProcessorErrorHandler(): callable
    {
        if ($this->invalidRouteErrorHandler === null) {
            $this->invalidRouteErrorHandler = [
                $this,
                'noProcessorFoundErrorHandler'
            ];
        }

        return $this->invalidRouteErrorHandler;
    }
}
