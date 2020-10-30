<?php

namespace App\Core;

/**
 * Class App
 *
 * @package App\Core
 */
class App
{
    /**
     * The router instance.
     *
     * @var Router
     */
    protected $router;


    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->setRouter(new Router());
    }


    /**
     * Used to configure the router.
     *
     * @param Router $router
     * @return $this
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;

        return $this;
    }


    /**
     * Returns the configured router.
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }
}