<?php
namespace Mezon\Router;

class RoutesSet
{

    /**
     * Mapping of routes to their execution functions for GET requests
     *
     * @var array
     */
    private $getRoutes = [];

    /**
     * Mapping of routes to their execution functions for GET requests
     *
     * @var array
     */
    private $postRoutes = [];

    /**
     * Mapping of routes to their execution functions for PUT requests
     *
     * @var array
     */
    private $putRoutes = [];

    /**
     * Mapping of routes to their execution functions for DELETE requests
     *
     * @var array
     */
    private $deleteRoutes = [];

    /**
     * Method returns list of routes for the HTTP method.
     *
     * @param string $method
     *            HTTP Method
     * @return array Routes
     */
    public function &getRoutesForMethod(string $method): array
    {
        switch ($method) {
            case ('GET'):
                $result = &$this->getRoutes;
                break;

            case ('POST'):
                $result = &$this->postRoutes;
                break;

            case ('PUT'):
                $result = &$this->putRoutes;
                break;

            case ('DELETE'):
                $result = &$this->deleteRoutes;
                break;

            default:
                throw (new \Exception('Unsupported request method'));
        }

        return $result;
    }

    /**
     * Method clears router data.
     */
    public function clear()
    {
        $this->getRoutes = [];

        $this->postRoutes = [];

        $this->putRoutes = [];

        $this->deleteRoutes = [];
    }

    /**
     * Method returns true if the router exists
     *
     * @param string $route
     *            checking route
     * @return bool true if the router exists, false otherwise
     */
    public function routeExists(string $route): bool
    {
        $allRoutes = array_merge($this->deleteRoutes, $this->putRoutes, $this->postRoutes, $this->getRoutes);

        return isset($allRoutes[$route]);
    }

    /**
     * Method rturns all available routes
     */
    public function getAllRoutesTrace()
    {
        return (count($this->getRoutes) ? 'GET:' . implode(', ', array_keys($this->getRoutes)) . '; ' : '') .
        (count($this->postRoutes) ? 'POST:' . implode(', ', array_keys($this->postRoutes)) . '; ' : '') .
        (count($this->putRoutes) ? 'PUT:' . implode(', ', array_keys($this->putRoutes)) . '; ' : '') .
        (count($this->deleteRoutes) ? 'DELETE:' . implode(', ', array_keys($this->deleteRoutes)) : '');
    }

    /**
     * Additing route for GET request
     * 
     * @param string $route route
     * @param object $object callback object
     * @param string $method callback method
     */
    public function addGetRoute(string $route, object $object, string $method):void{
        $this->getRoutes["/$route/"] = [
            $object,
            $method
        ];
    }

    /**
     * Additing route for GET request
     *
     * @param string $route route
     * @param object $object callback object
     * @param string $method callback method
     */
    public function addPostRoute(string $route, object $object, string $method):void{
        $this->postRoutes["/$route/"] = [
            $object,
            $method
        ];
    }
}