<?php

namespace App\Core;

/**
 * Class Session
 *
 * @package App\Core
 */
class Session
{
    /**
     * Indicates whether or not the session has been initialized.
     *
     * @var bool
     */
    protected $initialized = false;


    /**
     * Initializes the session.
     */
    public function start()
    {
        if($this->isInitialized()){
            return $this;
        }

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        return $this->setInitialized(true);
    }


    /**
     * Flushes the session.
     *
     * @return $this
     */
    public function flush()
    {
        $this->start();

        session_destroy();

        return $this;
    }


    /**
     * Registers a session key.
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;

        return $this;
    }


    /**
     * Returns a value from the session.
     *
     * @param $key
     * @param string $default
     * @return string
     */
    public function get($key, $default = '')
    {
        return get($_SESSION, $key, $default);
    }


    /**
     * Assigns the initialization status of the session.
     *
     * @param bool $flag
     * @return $this
     */
    public function setInitialized($flag = false)
    {
        $this->initialized = $flag;

        return $this;
    }


    /**
     * Indicates whether or not the session has been initialized.
     *
     * @return bool
     */
    public function isInitialized()
    {
        return $this->initialized;
    }
}