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
     * Identifies the request method.
     */
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';


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
     * Returns a piece of information from the server data.
     *
     * @param $key
     * @param string $default
     * @return string
     */
    public function server($key, $default = '')
    {
        return get($_SERVER, $key, $default);
    }


    /**
     * Assigns a key into the request object.
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }


    /**
     * Returns a global request parameter.
     *
     * @param $key
     * @param string $default
     * @return string
     *
     * @todo [High Priority] $_SERVER spoof vulnerability.
     * Need to move the $_SERVER access elsewhere to prevent a potential spoof opportunity
     * where a user might overwrite different parameters via form data.
     */
    public function get($key, $default = '')
    {
        if (!$this->getData()) {
            $_IN = json_decode(file_get_contents('php://input'), true) ?: [];

            $this->data = array_merge($_SERVER, $this->getData());
            $this->data = array_merge($_POST, $this->getData());
            $this->data = array_merge($_GET, $this->getData());
            $this->data = array_merge($_IN, $this->getData());
        }

        return get($this->getData(), $key, $default);
    }
    

    /**
     * Returns a specific header.
     *
     * @param $key
     * @param string $default
     * @return string
     */
    public function getHeader($key, $default = '')
    {
        $headers = getallheaders();

        foreach ($headers as $header => $value) {
            if (!strcasecmp($header, $key)) {
                return $value;
            }
        }

        return $default;
    }


    /**
     * Returns the current request method.
     *
     * @return string
     */
    public function getMethod()
    {
        return get($_SERVER, 'REQUEST_METHOD', self::METHOD_GET);
    }


    /**
     * Indicates whether or not the current request expects JSON.
     *
     * @return bool
     */
    public function isJson()
    {
        if (strpos($this->getHeader('Content-Type'), 'application/json') !== false) {
            return true;
        }

        return false;
    }
}