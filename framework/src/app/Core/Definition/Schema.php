<?php

namespace App\Core\Definition;

use App\Core\Traits\ConstructedFromArray;

/**
 * Class Schema
 *
 * @package App\Core\Definition
 */
class Schema
{
    use ConstructedFromArray;

    /**
     * The defined fields.
     *
     * @var array
     */
    public $fields = [
        'connection' => [
            'host' => '',
            'user' => '',
            'password' => '',
            'database' => ''
        ],
        'tables' => []
    ];


    /**
     * Returns all of the tables.
     *
     * @return mixed
     */
    public function getTables()
    {
        return $this->data['tables'];
    }


    /**
     * Returns the configured database.
     *
     * @return mixed
     */
    public function getDatabase()
    {
        return $this->data['connection']['database'];
    }


    /**
     * Returns the configured host.
     *
     * @return mixed
     */
    public function getHost()
    {
        return $this->data['connection']['host'];
    }


    /**
     * Returns the connection user.
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->data['connection']['user'];
    }


    /**
     * Returns the connection password.
     *
     * @return mixed
     */
    public function getPassword()
    {
        return $this->data['connection']['password'];
    }


    /**
     * Returns an individual table.
     *
     * @param $table
     * @return Table
     */
    public function getTable($table)
    {
        return new Table(
            array_get($this->data['tables'], $table, [])
        );
    }
}