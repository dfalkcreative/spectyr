<?php

namespace App\Core;

use Closure;

/**
 * Class Command
 *
 * @package App\Core
 */
class Command
{
    /**
     * The action to execute.
     *
     * @var Closure
     */
    protected $action;


    /**
     * Command constructor.
     */
    public function __construct()
    {

    }


    /**
     * Used to assign an action to execute if the command is called.
     *
     * @param Closure $action
     * @return $this
     */
    public function setAction(Closure $action)
    {
        $this->action = $action;

        return $this;
    }


    /**
     * Returns the configured action.
     *
     * @return Closure
     */
    public function getAction()
    {
        return $this->action;
    }


    /**
     * Used to execute the configured action.
     *
     * @param array $arguments
     */
    public function call($arguments = [])
    {
        if ($this->getAction()) {
            $this->getAction()($arguments);
        }
    }
}