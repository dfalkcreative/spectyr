<?php

namespace App\Core;

/**
 * Class Config
 *
 * @package App\Core
 */
class Config
{
    /**
     * The various configuration files.
     *
     * @var array
     */
    public $files = [];


    /**
     * Config constructor.
     *
     * @param string $directory
     */
    public function __construct($directory = '')
    {
        $this->getFromDirectory($directory);
    }


    /**
     * Reads all of the files from a directory.
     *
     * @param $directory
     * @return Config
     */
    public function getFromDirectory($directory = '')
    {
        if (!$directory || !file_exists($directory) || !is_dir($directory)) {
            return $this;
        }

        $files = scandir($directory);

        if (!has($files)) {
            return $this;
        }

        // Read each JSON configuration file.
        foreach ($files as $file) {
            if (is_dir($file)) {
                continue;
            }

            $file = new File($directory . DIRECTORY_SEPARATOR . $file);

            if ($file->getExtension() != 'json') {
                continue;
            }

            $this->files[$file->getName()] = $file->readFromJson();
        }

        return $this;
    }


    /**
     * Returns all of the discovered files.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }


    /**
     * Used to read an individual file.
     *
     * @param $key
     * @return mixed
     */
    public function getFile($key)
    {
        return array_get($this->getFiles(), $key, []);
    }


    /**
     * Maps a configuration file to a class.
     *
     * @param $key
     * @param $class
     * @return mixed
     */
    public function alias($key, $class)
    {
        return new $class($this->getFile($key));
    }
}