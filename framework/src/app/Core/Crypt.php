<?php

namespace App\Core;

/**
 * Class Crypt
 *
 * @package App\Core
 */
class Crypt
{
    /**
     * Apply bcrypt encryption to a value.
     *
     * @param string $value
     * @return bool|string
     */
    public static function hash($value = '')
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }


    /**
     * Verifies the hash against a string.
     *
     * @param string $hash
     * @param string $actual
     * @return bool
     */
    public static function verify($hash = '', $actual = '')
    {
        return password_verify($actual, $hash);
    }
}