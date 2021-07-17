<?php

namespace App\Core;

use Exception;
use App\Core\Exception\ViewNotFoundException;
use App\Core\Exception\RouteNotFoundException;
use App\Core\Exception\InvalidResponseException;

/**
 * Class Router
 *
 * @package App\Core
 */
class Router
{
    /**
     * The exception template to use.
     */
    const EXCEPTION_TEMPLATE = 'system/exception';


    /**
     * The routes list.
     *
     * @var array
     */
    protected $routes = [];


    /**
     * The request instance.
     *
     * @var Request
     */
    protected $request;


    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->setRequest(new Request());
    }


    /**
     * Used to configure the request object.
     *
     * @param Request $request
     * @return Router
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }


    /**
     * Returns the request object.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }


    /**
     * Used to configure a new route.
     *
     * @param $uri
     * @param Route $route
     * @return Router
     */
    public function addRoute($uri, Route $route)
    {
        $this->routes[$this->getRequest()->getPreparedUri($uri)] = $route;

        return $this;
    }


    /**
     * Used to register multiple routes.
     *
     * @param array $routes
     * @return Router
     */
    public function addRoutes($routes = [])
    {
        if (has($routes)) {
            foreach ($routes as $uri => $route) {
                $this->addRoute($uri, $route);
            }
        }

        return $this;
    }


    /**
     * Returns the route list.
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }


    /**
     * Returns the current route.
     *
     * @return Route
     */
    public function getCurrentRoute()
    {
        return get($this->getRoutes(), $this->getRequest()->getUri());
    }


    /**
     * Used to execute a route against the current URI.
     */
    public function getResponse()
    {
        try {
            // Validate the route.
            if (!($route = $this->getCurrentRoute()) instanceof Route) {
                throw new RouteNotFoundException(
                    __("This route has not been defined.")
                );
            }

            // Validate the response.
            if (!($response = $route->call($this->getRequest())) instanceof Response) {
                throw new InvalidResponseException(
                    __("Invalid controller response received.")
                );
            }

            // Render the response.
            $response->render();

        } catch (Exception $exception) {
            $view = view(self::EXCEPTION_TEMPLATE, [
                'exception' => $exception
            ]);

            if($view->exists()){
                $view->render();
            }
        }
    }
}
