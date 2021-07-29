<?php

namespace App\Core;

/**
 * Class Debug
 *
 * @package App\Core
 */
class Debug
{
    public function __construct()
    {
        ini_set('xdebug.var_display_max_depth', -1);
        ini_set('xdebug.var_display_max_children', -1);
        ini_set('xdebug.var_display_max_data', -1);
    }


    /**
     * Used to print a full dump of the specified variable.
     *
     * @param $variable
     */
    public function print($variable)
    {
        echo '<pre style="background: #eee;">';
        echo(var_dump($variable));
        echo '</pre>';
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