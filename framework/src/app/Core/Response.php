<?php

namespace App\Core;

/**
 * Class Response
 *
 * @package App\Core
 */
class Response
{
    /**
     * The appropriate response headers.
     *
     * @var array
     */
    protected $headers = [];


    /**
     * Any additional response data.
     *
     * @var
     */
    protected $data = [];


    /**
     * Response constructor.
     */
    public function __construct(){

    }


    /**
     * Used to display a response.
     */
    public function render()
    {
        if (!headers_sent()) {
            $this->headers();
        }
    }


    /**
     * Used to render the appropriate headers.
     */
    public function headers()
    {
        if(!$this->getHeaders()){
            return;
        }

        // Render each header.
        foreach($this->getHeaders() as $header => $value){
            header("$header: $value");
        }
    }


    /**
     * Used to configure the appropriate headers.
     *
     * @param array $headers
     * @return $this
     */
    public function setHeaders($headers = [])
    {
        $this->headers = $headers;

        return $this;
    }


    /**
     * Returns the configured headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }


    /**
     * Used to set the response data.
     *
     * @param array $data
     * @return $this
     */
    public function setData($data = [])
    {
        $this->data = $data;

        return $this;
    }


    /**
     * Returns the configured response data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}