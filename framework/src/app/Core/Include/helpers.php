<?php

use App\Core\View;


function __($message = '')
{
    return $message;
}


/**
 * Returns a variable property.
 *
 * @param $variable
 * @param $key
 * @param string $default
 * @return string
 */
function get($variable, $key, $default = '')
{
    if (is_array($variable) && isset($variable[$key])) {
        return $variable[$key];
    }

    return $default;
}


/**
 * Indicates whether or not an array has information.
 *
 * @param $variable
 * @return bool
 */
function has($variable)
{
    if (!is_array($variable) || !$variable || !count($variable)) {
        return false;
    }

    return true;
}


/**
 * Returns a new view instance.
 *
 * @param $template
 * @param array $data
 * @return View
 */
function view($template, $data = [])
{
    return new View($template, $data);
}


/**
 * Used to dump the contents.
 *
 * @param $contents
 */
function dump($contents)
{
    var_dump($contents);
}


/**
 * Used to output debugging information and kill execution.
 *
 * @param $contents
 */
function dd($contents)
{
    var_dump($contents);
    die();
}