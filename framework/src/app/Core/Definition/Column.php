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
     * Used to convert the set type into one that SQL understands.
     */
    public function getSqlType()
    {
        switch(strtolower($this->getType())){
            case 'date':
            case 'datetime':
                $type = 'datetime';
                break;

            case 'currency':
            case 'decimal':
            case 'float':
                $type = 'decimal';
                break;

            case 'integer':
                $type = 'int';
                break;

            case 'text':
                $type = 'text';
                break;

            case 'longtext':
                $type = 'longtext';
                break;

            case 'varchar':
            case 'string':
            default:
                $type = 'varchar(191)';
                break;
        }

        return $type;
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