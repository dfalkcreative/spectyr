<?php

namespace App\Core;

use App\Core\Request;
use App\Core\Controller;
use App\Core\Exception\InvalidControllerException;

/**
 * Class Route
 *
 * @package App\Core
 */
class Route
{
    /**
     * The controller class.
     *
     * @var
     */
    protected $controller;


    /**
     * The action to execute.
     *
     * @var
     */
    protected $action;


    /**
     * Route constructor.
     *
     * @param string $controller
     * @param string $action
     */
    public function __construct($controller = '', $action = '')
    {
        $this->setController($controller)->setAction($action);
    }


    /**
     * Used to assign the controller.
     *
     * @param string $controller
     * @return $this
     */
    public function setController($controller = '')
    {
        $this->controller = $controller;

        return $this;
    }


    /**
     * Returns the configured controller.
     *
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }


    /**
     * Used to configure the action.
     *
     * @param string $action
     * @return $this
     */
    public function setAction($action = '')
    {
        $this->action = $action;

        return $this;
    }


    /**
     * Returns the configured action.
     *
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }


    /**
     * Used to execute the configured controller.
     *
     * @return mixed
     * @throws InvalidControllerException
     */
    public function call(Request $request)
    {
        $action = $this->getAction();

        // Verify that the controller exists.
        if (!class_exists($controller = $this->getController())) {
            return false;
        }

        // Verify that the controller is valid.
        if(!($instance = new $controller()) instanceof Controller){
            throw new InvalidControllerException(
                __("Invalid controller specified for route.")
            );
        }

        // Verify that the action exists.
        if (!method_exists($instance, $action)) {
            return false;
        }

        // Share some additional information.
        $instance->setRequest($request);

        return $instance->$action();
    }
}