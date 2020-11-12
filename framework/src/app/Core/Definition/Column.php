<?php

namespace App\Core\Definition;

use App\Core\Traits\ConstructedFromArray;

/**
 * Class Column
 *
 * @package App\Core\Definition
 */
class Column
{
    use ConstructedFromArray;

    /**
     * The definition schema.
     *
     * @var array
     */
    public $fields = [
        'name' => '',
        'type' => '',
        'default' => '',
        'nullable' => true,
    ];


    /**
     * Returns the configured name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->data['name'];
    }


    /**
     * Returns the column type.
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->data['type'];
    }


    /**
     * Returns the default value.
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->data['default'];
    }


    /**
     * Indicates whether or not the column is nullable.
     *
     * @return mixed
     */
    public function getNullable()
    {
        return $this->data['nullable'];
    }
}