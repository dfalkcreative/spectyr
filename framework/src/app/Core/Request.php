<?php

namespace App\Core;

/**
 * Class Request
 *
 * @package App\Core
 */
class Request
{
    /**
     * The current URI.
     *
     * @var
     */
    protected $uri;


    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->uri = $this->get('REQUEST_URI');
    }


    /**
     * Returns the current URI.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->getPreparedUri($this->uri);
    }


    /**
     * Used to clean a URI for various operations like routing, comparisons, etc.
     *
     * @param $uri
     * @return bool|string
     */
    public function getPreparedUri($uri)
    {
        // Ignore the URI.
        $uri = explode('?', $uri)[0];

        // Handle trailing slash.
        if (substr($uri, -1) == '/') {
            while ($uri && substr($uri, -1) == '/') {
                $uri = substr($uri, 0, strlen($uri) - 1);
            }
        }

        // Handle leading slash.
        if (substr($uri, 0, 1) == '/') {
            while ($uri && substr($uri, 0, 1) == '/') {
                $uri = substr($uri, 1, strlen($uri) - 1);
            }
        }

        return $uri;
    }


    /**
     * Returns a global request parameter.
     *
     * @param $key
     * @param string $default
     * @return string
     */
    public function get($key, $default = '')
    {
        return get($_GET, $key, get($_POST, $key, get($_SERVER, $key, $default)));
    }
}