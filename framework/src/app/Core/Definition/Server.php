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
        return $this->data['DOCUMENT_ROOT'];
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
}