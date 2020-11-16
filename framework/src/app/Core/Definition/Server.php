<?php

namespace App\Core\Definition;

use App\Core\Traits\ConstructedFromArray;

/**
 * Class Server
 *
 * @package App\Core\Definition
 */
class Server
{
    use ConstructedFromArray;

    /**
     * The definition schema.
     *
     * @var array
     */
    public $fields = [
        'argv' => '',
        'REDIRECT_URL' => '',
        'DOCUMENT_ROOT' => '',
        'REDIRECT_STATUS' => '',
    ];


    /**
     * Server constructor.
     */
    public function __construct()
    {
        $this->fromArray($_SERVER);
    }


    /**
     * Returns the document root.
     *
     * @return mixed
     */
    public function getDocumentRoot()
    {
        return $this->data['DOCUMENT_ROOT'] ?: getcwd();
    }


    /**
     * Returns a directory path relative to the document root.
     *
     * @param string $path
     * @return string
     */
    public function getPath($path = '')
    {
        return $this->getDocumentRoot() . DIRECTORY_SEPARATOR . $path;
    }


    /**
     * Returns the redirect URL.
     *
     * @return mixed
     */
    public function getRedirectUrl()
    {
        return $this->data['REDIRECT_URL'];
    }


    /**
     * Returns the redirect status.
     *
     * @return mixed
     */
    public function getRedirectStatus()
    {
        return $this->data['REDIRECT_STATUS'];
    }


    /**
     * Returns any arguments passed to the server.
     *
     * @return mixed
     */
    public function getArguments()
    {
        return $this->data['argv'];
    }
}