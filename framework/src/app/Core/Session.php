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
     * Initializes the session.
     */
    public function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $this;
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
}