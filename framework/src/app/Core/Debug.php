<?php

namespace App\Core;

/**
 * Class Debug
 *
 * @package App\Core
 */
class Debug
{
    /**
     * Used to print a full dump of the specified variable.
     *
     * @param $variable
     */
    public function print($variable)
    {
        echo '<pre>';
        die(var_dump($variable));
        echo '</pre>';

        switch (true) {
            case is_object($variable):
                break;

            case is_array($variable):
                foreach ($variable as $key => $value) {
                    $this->open($key);
                    $this->print($value);
                    $this->close();
                }
                break;

            default:
                $this->line(print_r($variable));
                break;
        }
    }


    /**
     * Prints an individual line,
     *
     * @param $value
     */
    public function line($value){
        echo "<div class='dump__line'>$value</div>";
    }


    /**
     * Opens a new debug wrapper.
     *
     * @param $label
     */
    public function open($label)
    {
        echo "<div class='dump__wrapper'><span class='dump__label'>$label</span>";
    }


    /**
     * Closes the wrapper.
     */
    public function close()
    {
        echo "</div>";
    }
}