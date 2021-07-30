<?php

use App\Core\App;
use App\Core\Query;
use App\Core\Model;
use App\Core\Debug;
use App\Core\Response\Json;
use App\Core\Response\View;


/**
 * Returns a localized message.
 *
 * @param string $message
 * @return string
 */
function __($message = '')
{
    return $message;
}


/**
 * Used to retrieve an environment variable.
 *
 * @param $key
 * @param string $default
 * @return string
 */
function env($key, $default = '')
{
    return array_get($_ENV, $key, $default);
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
        $value = $variable[$key];

        if (is_string($value)) {
            return trim($value);
        }

        return $value;
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
 * Returns the configured application instance.
 *
 * @return App
 */
function app()
{
    return App::getInstance();
}


/**
 * Redirect to another page.
 *
 * @param string $url
 */
function redirect($url = '')
{
    return app()->getRouter()->redirect($url);
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
 * Used to output a JSON response.
 *
 * @param $data
 * @return Json
 */
function json($data)
{
    return new Json($data);
}


/**
 * Returns a new query instance.
 *
 * @return Query
 */
function query()
{
    return new Query();
}


/**
 * Returns the global request.
 *
 * @return \App\Core\Request
 */
function request()
{
    return App::getInstance()->getRouter()->getRequest();
}


/**
 * Returns the assigned session handler.
 *
 * @return \App\Core\Session
 */
function session()
{
    return App::getInstance()->getSession();
}


/**
 * Returns a new model instance.
 *
 * @param string $name
 */
function model($name = '')
{
    return new Model($name);
}


/**
 * Used to dump the contents.
 *
 * @param $contents
 */
function dump($contents)
{
    App::debugger()->print($contents);
}


/**
 * Used to output debugging information and kill execution.
 *
 * @param $contents
 */
function dd($contents)
{
    App::debugger()->print($contents);
    die();
}


/**
 * Flatten a multi-dimensional associative array with dots.
 *
 * @param  array $array
 * @param  string $prepend
 * @return array
 */
function array_dot($array, $prepend = '')
{
    $results = [];

    foreach ($array as $key => $value) {
        if (is_array($value) && !empty($value)) {
            $results = array_merge($results, array_dot($value, $prepend . $key . '.'));
        } else {
            $results[$prepend . $key] = $value;
        }
    }

    return $results;
}


/**
 * Get an item from an array using "dot" notation.
 *
 * @param  \ArrayAccess|array $array
 * @param  string|int $key
 * @param  mixed $default
 * @return mixed
 */
function array_get($array, $key, $default = null)
{
    if (!is_array($array)) {
        return $default;
    }

    if (is_null($key)) {
        return $array;
    }

    if (array_key_exists($key, $array)) {
        return $array[$key];
    }

    if (strpos($key, '.') === false) {
        return $array[$key] ?? $default;
    }

    foreach (explode('.', $key) as $segment) {
        if (is_array($array) && array_key_exists($segment, $array)) {
            $array = $array[$segment];
        } else {
            return $default;
        }
    }

    return $array;
}


/**
 * Generates a unique token.
 *
 * @return string
 */
function token()
{
    $data = openssl_random_pseudo_bytes(16);
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s%s', str_split(bin2hex($data), 4));
}


/**
 * Generates a unique ID.
 *
 * @return string
 */
function uuid()
{
    $data = openssl_random_pseudo_bytes(16);
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


/**
 * Returns all positions of a string occurrence.
 *
 * @param $haystack
 * @param $needle
 * @return array
 */
function strpos_all($haystack, $needle)
{
    $offset = 0;
    $positions = [];

    while (($position = strpos($haystack, $needle, $offset)) !== false) {
        $offset = $position + 1;
        $positions[] = $position;
    }

    return $positions;
}