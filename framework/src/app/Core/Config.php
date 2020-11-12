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
     */
    public function __construct()
    {

    }


    /**
     * Reads all of the files from a directory.
     *
     * @param $directory
     */
    public function getFromDirectory($directory)
    {
        if (!file_exists($directory) || !is_dir($directory)) {
            return;
        }

        $files = scandir($directory);

        if (!has($files)) {
            return;
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