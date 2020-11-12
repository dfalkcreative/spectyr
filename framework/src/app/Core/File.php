<?php

namespace App\Core;

/**
 * Class File
 *
 * @package App\Core
 */
class File
{
    /**
     * The file path.
     *
     * @var
     */
    public $path;


    /**
     * The base file name.
     *
     * @var
     */
    public $name;


    /**
     * The extension.
     *
     * @var
     */
    public $extension;


    /**
     * File constructor.
     *
     * @param string $path
     */
    public function __construct($path = '')
    {
        $this->setPath($path);

        // Determine whether or not the file exists.
        if (!$this->exists()) {
            return;
        }

        // Assign additional details.
        $details = pathinfo($path);

        $this->setExtension(array_get($details, 'extension'))
            ->setName(array_get($details, 'basename'));
    }


    /**
     * Configures the file path.
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path = '')
    {
        $this->path = $path;

        return $this;
    }


    /**
     * Returns the file path.
     *
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }


    /**
     * Used to assign the file extension.
     *
     * @param string $extension
     * @return $this
     */
    public function setExtension($extension = '')
    {
        $this->extension = $extension;

        return $this;
    }


    /**
     * Returns the file extension.
     *
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension ? strtolower($this->extension) : '';
    }


    /**
     * Used to assign the name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name = '')
    {
        $this->name = basename($name, ".{$this->getExtension()}");

        return $this;
    }


    /**
     * Returns the file name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Indicates whether or not the file exists.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->path && file_exists($this->path);
    }


    /**
     * Used to read the file.
     *
     * @return mixed
     */
    public function read()
    {
        if (!$this->exists()) {
            return false;
        }

        return file_get_contents($this->getPath());
    }


    /**
     * Reads a file from JSON.
     *
     * @return array
     */
    public function readFromJson()
    {
        return json_decode($this->read(), true) ?: [];
    }
}