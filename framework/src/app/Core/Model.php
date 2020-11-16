<?php

namespace App\Core;

use App\Core\Definition\Table;

/**
 * Class Model
 *
 * @package App\Core
 */
class Model extends Query
{
    /**
     * The table instance for the model.
     *
     * @var Table
     */
    protected $table;


    /**
     * The returned attributes from queries.
     *
     * @var array
     */
    protected $attributes = [];


    /**
     * The primary key.
     *
     * @var string
     */
    protected $primary = 'id';


    /**
     * Model constructor.
     *
     * @param string $table
     */
    public function __construct($table = '')
    {
        parent::__construct();

        $this->setTable(app()->getSchema()->getTable($table))
            ->table($this->getTable()->getTable());
    }


    /**
     * Returns an attribute.
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }


    /**
     * Used to set an attribute.
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }


    /**
     * Casts an instance to an array.
     *
     * @return array
     */
    public function __serialize()
    {
        return $this->getAttributes();
    }


    /**
     * Casts an object to a string.
     *
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this->getAttributes());
    }


    /**
     * Casts an object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getAttributes();
    }


    /**
     * Returns the primary key.
     *
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primary;
    }


    /**
     * Used to assign the table instance.
     *
     * @param Table $table
     * @return Model
     */
    public function setTable(Table $table)
    {
        $this->table = $table;

        return $this;
    }


    /**
     * Returns the table instance.
     *
     * @return Table
     */
    public function getTable()
    {
        return $this->table;
    }


    /**
     * Used to assign attributes to the model.
     *
     * @param array $attributes
     * @return $this
     */
    public function setAttributes($attributes = [])
    {
        $this->attributes = $attributes;

        return $this;
    }


    /**
     * Returns the configured attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }


    /**
     * Returns an individual attribute.
     *
     * @param $attribute
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        return array_get($this->getAttributes(), $attribute);
    }


    /**
     * Used to assign an attribute.
     *
     * @param $attribute
     * @param string $value
     * @return $this
     */
    public function setAttribute($attribute, $value = '')
    {
        $this->attributes[$attribute] = $value;

        return $this;
    }


    /**
     * Used to save any changes to a model.
     *
     * @throws Exception\QueryException
     */
    public function save()
    {
        if (!$this->getAttribute($this->getPrimaryKey())) {
            $result = $this->fresh()
                ->create($this->getAttributes());

            $this->setAttributes($result);
            return;
        }

        $this->fresh()
            ->where($this->getPrimaryKey(), '=', $this->getAttribute($this->getPrimaryKey()))
            ->update($this->getAttributes());
    }


    /**
     * Returns a fresh instance of the current record.
     *
     * @return Model
     */
    public function fresh()
    {
        return (new Model($this->getTarget()))
            ->setAttributes($this->getAttributes());
    }


    /**
     * Used to return a new instance of this object.
     *
     * @param array $attributes
     * @return Model|array
     */
    public function instance($attributes = [])
    {
        return (new Model($this->getTarget()))
            ->setAttributes($attributes);
    }
}