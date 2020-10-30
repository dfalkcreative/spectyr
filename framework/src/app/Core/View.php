<?php

namespace App\Core;

use Closure;
use App\Core\Exception\ViewNotFoundException;

/**
 * Class View
 *
 * @package App\Core
 */
class View extends Response
{
    /**
     * The template format to look for.
     */
    const TEMPLATE_FORMAT = 'php';


    /**
     * The template root directory.
     */
    const TEMPLATE_ROOT_DIRECTORY = 'resources/views';


    /**
     * The template cache directory.
     */
    const TEMPLATE_CACHE_DIRECTORY = 'storage/cache/views';


    /**
     * The template path.
     *
     * @var
     */
    protected $path;


    /**
     * The values used for the template.
     *
     * @var
     */
    protected $data;


    /**
     * The sections of the template.
     *
     * @var array
     */
    protected $sections = [];


    /**
     * View constructor.
     *
     * @param string $path
     * @param array $data
     */
    public function __construct($path = '', $data = [])
    {
        $this->setPath($path);
        $this->setData($data);
    }


    /**
     * Used to render the view.
     *
     * @throws ViewNotFoundException
     */
    public function render()
    {
        // Verify that the template exists.
        if (!file_exists($this->getFullPath())) {
            throw new ViewNotFoundException(
                __("No view exists at {$this->getFullPath()}.")
            );
        }

        // Instantiate any variables for the view.
        if (has($this->getData())) {
            foreach ($this->getData() as $key => $value) {
                $$key = $value;
            }
        }

        // Include the template.
        include($this->getFullPath());
    }


    /**
     * Returns the configured sections for a view.
     *
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }


    /**
     * Indicates whether or not a section exists.
     *
     * @param $section
     * @return bool
     */
    public function hasSection($section)
    {
        return isset($this->sections[$section]);
    }


    /**
     * Used to append new contents to a section.
     *
     * @param $section
     * @param Closure $callback
     * @return View
     */
    public function setSection($section, Closure $callback)
    {
        if (!$this->hasSection($section)) {
            $this->sections[$section] = [];
        }

        // Register the section.
        $this->sections[$section][] = $callback;

        return $this;
    }


    /**
     * Used to render child sections.
     *
     * @param $section
     * @throws ViewNotFoundException
     */
    public function getSection($section)
    {
        // Verify that the section was registered.
        if (!$this->hasSection($section)) {
            return;
        }

        // Verify that the section is being utilized.
        if(!has($this->getSections()[$section])){
            return;
        }

        // Iterate through each child.
        foreach ($this->getSections()[$section] as $template) {
            if (!$template instanceof Closure) {
                continue;
            }

            // Retrieve the response.
            $response = $template();

            // Render the response, if applicable.
            if ($response instanceof View) {
                $response->render();
            }
        }
    }


    /**
     * Used to extends from a layout.
     *
     * @param $layout
     * @return View
     */
    public function layout($layout)
    {
        $this->setPath($layout);

        return $this;
    }


    /**
     * Returns the full path.
     *
     * @return string
     */
    public function getFullPath()
    {
        return self::TEMPLATE_ROOT_DIRECTORY . "/{$this->getPath()}." . self::TEMPLATE_FORMAT;
    }


    /**
     * Used to configure the path.
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
     * Returns the path.
     *
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }


    /**
     * Used to configure the variables for the view.
     *
     * @param array $data
     * @return $this
     */
    public function setData($data = [])
    {
        $this->data = $data;

        return $this;
    }


    /**
     * Returns the variables used for the template.
     *
     * @return $this
     */
    public function getData()
    {
        return $this->data;
    }
}