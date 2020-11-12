<?php

namespace App\Core\Traits;

/**
 * Trait ConstructedFromArray
 *
 * @package App\Core\Traits
 */
trait ConstructedFromArray
{
    /**
     * The transformed output from the creation.
     *
     * @var array
     */
    public $data = [];


    /**
     * The original input of data.
     *
     * @var array
     */
    protected $original = [];


    /**
     * ConstructedFromArray constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->setOriginal($data)->fromArray($data);
    }


    /**
     * Used to record the original object information.
     *
     * @param array $data
     * @return $this
     */
    public function setOriginal($data = [])
    {
        $this->original = $data;

        return $this;
    }


    /**
     * Returns the original input which constructed the object.
     *
     * @return array
     */
    public function getOriginal()
    {
        return $this->original;
    }


    /**
     * Returns the full data property of the object.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * Used to find all of the changed fields.
     *
     * @return array
     */
    public function findChanges()
    {
        $changes = [];
        $fields = array_dot($this->fields);
        $data = array_dot($this->data);

        foreach ($data as $key => $value) {
            if ($value != array_get($fields, $key)) {
                $changes[] = $key;
            }
        }

        return $changes;
    }


    /**
     * Returns a nested data parameter in dot notation.
     *
     * @param $parameter
     * @param string $fallback
     * @param bool $string
     * @return mixed
     */
    public function getParameter($parameter, $fallback = '', $string = false)
    {
        $value = array_get($this->data, $parameter, $fallback);

        if ($string && is_array($value)) {
            $value = implode('', $value);
        }

        return $value;
    }


    /**
     * Used to manually configure the data for this object.
     *
     * @param array $data
     */
    public function setData($data = [])
    {
        $this->data = $data;
    }


    /**
     * Used to create a new configuration object from a parsed JSON array.
     *
     * @param $array
     * @param array $base
     * @param bool $initial
     * @param bool|array $storage
     */
    public function fromArray($array, $base = [], $initial = true, &$storage = false)
    {
        if (!property_exists($this, 'fields')) {
            return;
        }

        if ($initial) {
            $base = $this->fields;
        }

        if ($storage === false) {
            $storage = &$this->data;
        }

        foreach ($base as $key => $item) {
            if (has($item) && array_key_exists($key, $array)) {
                $storage[$key] = [];
                $this->fromArray($array[$key], $item, false, $storage[$key]);
                continue;
            }

            $storage[$key] = is_array($array) && array_key_exists($key, $array) ? $array[$key] : $item;
        }
    }
}