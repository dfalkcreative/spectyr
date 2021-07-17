<?php

namespace App\Core\Response;

use Closure;
use App\Core\Response;
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
        $this->setPath($path)
            ->setData($data)
            ->setHeaders([
                'Content-Type' => 'text/html'
            ]);
    }


    /**
     * Indicates whether or not the view exists.
     *
     * @return bool
     */
    public function exists()
    {
        return file_exists($this->getFullPath());
    }


    /**
     * Used to render the view.
     *
     * @throws ViewNotFoundException
     */
    public function render()
    {
        parent::render();

        // Verify that the template exists.
        if (!$this->exists()) {
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
     * Yields a section content.
     *
     * @param $section
     * @param Closure $callback
     * @return View
     */
    public function yield($section, Closure $callback)
    {
        return $this->setSection($section, $callback);
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
     * Shorthand section yield.
     *
     * @param $section
     * @throws ViewNotFoundException
     */
    public function section($section)
    {
        $this->getSection($section);
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
        if (!has($this->getSections()[$section])) {
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
     * Used to extend from a layout.
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
     * Renders the view.
     *
     * @throws ViewNotFoundException
     */
    public function __toString()
    {
        ob_start();
        $this->render();
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }
}