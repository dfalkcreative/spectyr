<?php

namespace App\Core\Definition;

use App\Core\Traits\ConstructedFromArray;

/**
 * Class Schema
 *
 * @package App\Core\Definition
 */
class Table
{
    use ConstructedFromArray;

    /**
     * The definition schema.
     *
     * @var array
     */
    public $fields = [
        'name' => '',
        'table' => '',
        'single' => '',
        'columns' => [],
        'relations' => [],
    ];


    /**
     * Returns the name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->data['name'];
    }


    /**
     * Returns the table.
     *
     * @return mixed
     */
    public function getTable()
    {
        return $this->data['table'];
    }


    /**
     * Returns the singular name.
     *
     * @return mixed
     */
    public function getSingle()
    {
        return $this->data['single'];
    }


    /**
     * Returns the columns.
     *
     * @return mixed
     */
    public function getColumns()
    {
        return $this->data['columns'];
    }


    /**
     * Returns the relations.
     *
     * @return mixed
     */
    public function getRelations()
    {
        return $this->data['relations'];
    }


    /**
     * Returns a specific option for the model.
     *
     * @param $column
     * @return Column
     */
    public function getColumn($column)
    {
        return new Column(
            array_get($this->data['properties'], $column, [])
        );
    }
}