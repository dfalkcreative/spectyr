<?php

namespace App\Core;

/**
 * Class Date
 *
 * @package App\Core
 */
class Date
{
    /**
     * The input date to parse.
     *
     * @var string
     */
    protected $input;


    /**
     * Date constructor.
     *
     * @param string $input
     */
    public function __construct($input = '')
    {
        $this->input = $input;
    }


    /**
     * Used to return the date on string casts.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getInput();
    }


    /**
     * Used to assign an input.
     *
     * @param $input
     * @return $this
     */
    public function setInput($input)
    {
        $this->input = $input;

        return $this;
    }


    /**
     * Returns the input.
     *
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }
}