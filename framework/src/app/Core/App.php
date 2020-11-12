<?php

namespace App\Core;

use App\Core\Definition\Schema;
use App\Core\Definition\Server;

/**
 * Class App
 *
 * @package App\Core
 */
class App
{
    /**
     * The application instance.
     *
     * @var
     */
    public static $instance;


    /**
     * The router instance.
     *
     * @var Router
     */
    protected $router;


    /**
     * The configuration instance.
     *
     * @var
     */
    protected $config;


    /**
     * The server definition.
     *
     * @var
     */
    protected $server;


    /**
     * The database migrator.
     *
     * @var
     */
    protected $migrator;


    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->setServer(new Server())
            ->setRouter(new Router())
            ->setConfig(new Config())
            ->setMigrator(new Migrator())
            ->setEnvironment();

        // Read configuration data.
        $this->getConfig()->getFromDirectory(
            $this->getServer()->getDocumentRoot() . DIRECTORY_SEPARATOR . 'config'
        );

        // Migrate the application.
        $this->getMigrator()->setSchema(
            $this->getConfig()->alias('schema', Schema::class)
        )->build();

        // Assign the instance.
        static::setInstance($this);
    }


    /**
     * Used to load any environment variables.
     *
     * @return mixed
     */
    public function setEnvironment()
    {
        if (file_exists($this->getServer()->getDocumentRoot() . DIRECTORY_SEPARATOR . '.env')) {
            \Dotenv\Dotenv::createImmutable($this->getServer()->getDocumentRoot())->load();
        }

        return $this;
    }


    /**
     * Used to assign a migrator to the application.
     *
     * @param Migrator $migrator
     * @return $this
     */
    public function setMigrator(Migrator $migrator)
    {
        $this->migrator = $migrator;

        return $this;
    }


    /**
     * Returns the configured migrator.
     *
     * @return mixed
     */
    public function getMigrator()
    {
        return $this->migrator;
    }


    /**
     * Used to configure the router.
     *
     * @param Router $router
     * @return $this
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;

        return $this;
    }


    /**
     * Returns the configured router.
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }


    /**
     * Used to assign a configuration handler.
     *
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }


    /**
     * Returns the configuration handler for the application.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }


    /**
     * Used to configure the server object.
     *
     * @param Server $server
     * @return $this
     */
    public function setServer(Server $server)
    {
        $this->server = $server;

        return $this;
    }


    /**
     * Returns the server instance.
     *
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }


    /**
     * Used to assign the global application instance.
     *
     * @param $instance
     */
    public static function setInstance($instance)
    {
        self::$instance = $instance;
    }


    /**
     * Returns the configured instance.
     *
     * @return App
     */
    public static function getInstance()
    {
        return self::$instance;
    }
}