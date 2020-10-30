<?php

namespace App\Core;

/**
 * Class Controller
 *
 * @package App\Core
 */
class Controller
{
    /**
     * The request object to reference for various controller actions.
     *
     * @var
     */
    protected $request;


    /**
     * Used to configure the request object.
     *
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }


    /**
     * Returns the configured request object.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}