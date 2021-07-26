<?php

namespace App\Core;

use Closure;
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
     * Handles specific exception mappings.
     *
     * @var array
     */
    protected $exceptions = [];


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
     * Redirects to another URL.
     *
     * @param string $url
     */
    public function redirect($url = '')
    {
        header("Location: $url");
        die();
    }


    /**
     * Registers a new exception.
     *
     * @param $class
     * @param Closure $callback
     * @return $this
     */
    public function addException($class, Closure $callback)
    {
        $this->exceptions[$class] = $callback;

        return $this;
    }


    /**
     * Registers a series of exceptions.
     *
     * @param array $exceptions
     * @return $this
     */
    public function addExceptions($exceptions = [])
    {
        foreach ($exceptions as $class => $callback) {
            if (!$callback instanceof Closure) {
                continue;
            }

            $this->addException($class, $callback);
        }

        return $this;
    }


    /**
     * Indicates whether or not an exception is defined.
     *
     * @param $class
     * @return bool
     */
    public function hasException($class)
    {
        return isset($this->exceptions[$class]);
    }


    /**
     * Returns specific exception functionality.
     *
     * @param $class
     * @return mixed
     */
    public function getException($class)
    {
        if ($this->hasException($class)) {
            return $this->exceptions[$class];
        }

        return false;
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
        $current = array_filter(explode('/', $this->getRequest()->getUri()));
        $matched = null;
        $parameters = [];

        // Determine which route we're accessing.
        foreach($this->getRoutes() as $uri => $route){
            $parameters = [];
            $segments = array_filter(explode('/', $uri));
            $valid = true;

            if(count($segments) !== count($current)){
                continue;
            }

            // Parse each individual segment to identify parameters.
            foreach($segments as $i => $segment){
                $segment = trim($segment);

                if(substr($segment, 0, 1) === ':'){
                    $parameters[substr($segment, 1)] = $current[$i];
                    continue;
                }

                if($segments[$i] !== $current[$i]){
                    $valid = false;
                    break;
                }
            }

            // Check for a match.
            if($valid){
                $matched = $route;
                break;
            }
        }

        return $matched instanceof Route ?
            $matched->setParameters($parameters) : $matched;
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
            $callback = $this->getException(get_class($exception));

            if ($callback instanceof Closure) {
                return $callback($exception);
            }

            $view = view(self::EXCEPTION_TEMPLATE, [
                'exception' => $exception
            ]);

            if ($view->exists()) {
                $view->render();
            }
        }
    }
}
