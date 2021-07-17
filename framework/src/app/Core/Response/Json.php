<?php

namespace App\Core\Response;

use App\Core\Response;

/**
 * Class Json
 *
 * @package App\Core\Response
 */
class Json extends Response
{
    /**
     * The data to serialize for display.
     *
     * @var array
     */
    public $data = [];


    /**
     * Json constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->setData($data);
        $this->setHeaders([
            'Content-Type' => 'application/json'
        ]);
    }


    /**
     * Output the JSON content.
     */
    public function render()
    {
        parent::render();

        echo json_encode($this->getData());
    }


    /**
     * Used to configure the variables for the view.
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
     * Returns the variables used for the template.
     *
     * @return array
     */
    public function getData()
    {
        $response = [];

        if(!has($this->data)){
            return $response;
        }

        foreach($this->data as $key => $element){
            if(is_object($element) && method_exists($element, 'toArray')){
                $response[$key] = $element->toArray();
                continue;
            }

            $response[$key] = $element;
        }

        return $response;
    }
}