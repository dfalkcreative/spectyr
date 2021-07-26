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
     * The input data.
     *
     * @var array
     */
    protected $data = [];


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
        // Ignore any request parameters.
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
     * Returns the entire request collection.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
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
        if (!$this->data) {
            $_IN = json_decode(file_get_contents('php://input'), true) ?: [];

            $this->data = array_merge($_SERVER, $this->data);
            $this->data = array_merge($_POST, $this->data);
            $this->data = array_merge($_GET, $this->data);
            $this->data = array_merge($_IN, $this->data);
        }

        return get($this->data, $key, $default);
    }
}