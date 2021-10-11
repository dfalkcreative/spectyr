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
     * The migration command prefix.
     */
    const COMMAND_MIGRATE = 'migrate';


    /**
     * Indicates the schema target to reference.
     */
    const CONFIGURATION_SCHEMA = 'schema';


    /**
     * Indicates the assigned directory for configuration files.
     */
    const DIRECTORY_CONFIGURATION = 'config';


    /**
     * The environment variable keys.
     */
    const ENVIRONMENT_DB_NAME = 'DB_NAME';
    const ENVIRONMENT_DB_HOST = 'DB_HOST';
    const ENVIRONMENT_DB_PORT = 'DB_PORT';
    const ENVIRONMENT_DB_USER = 'DB_USER';
    const ENVIRONMENT_DB_PASS = 'DB_PASS';
    const ENVIRONMENT_DB_CHARSET = 'DB_CHARSET';


    /**
     * The application instance.
     *
     * @var App
     */
    public static $instance;


    /**
     * The router instance.
     *
     * @var Router
     */
    protected $router;


    /**
     * The logger instance.
     *
     * @var Logger
     */
    protected $logger;


    /**
     * The configuration instance.
     *
     * @var Config
     */
    protected $config;


    /**
     * The server definition.
     *
     * @var Server
     */
    protected $server;


    /**
     * The session handler.
     *
     * @var Session
     */
    protected $session;


    /**
     * The database migrator.
     *
     * @var Migrator
     */
    protected $migrator;


    /**
     * The database map file.
     *
     * @var Schema
     */
    protected $schema;


    /**
     * The console handler for the application.
     *
     * @var Console
     */
    protected $console;


    /**
     * The debugger to use.
     *
     * @var Debugger
     */
    protected $debugger;


    /**
     * App constructor.
     */
    public function __construct()
    {
        // Bootstrap any dependencies.
        $this->setDebugger(new Debug())
            ->setSession(new Session())
            ->setConsole(new Console())
            ->setServer(new Server())
            ->setLogger(new Logger())
            ->setRouter(new Router())
            ->setConfig(new Config(
                $this->getServer()->getPath(self::DIRECTORY_CONFIGURATION)))
            ->setSchema(new Schema(
                $this->getConfig()->getFile(self::CONFIGURATION_SCHEMA)))
            ->setMigrator(new Migrator(
                $this->getSchema()
            ))->setEnvironment();

        // Build out any additional commands.
        $this->getConsole()
            ->addCommand(self::COMMAND_MIGRATE, (new Command())
                ->setAction(function () {
                    app()->getMigrator()->build();
                })
            );

        // Assign the instance.
        static::setInstance($this);
    }


    /**
     * Creates a new App instance.
     *
     * @return App
     */
    public static function create()
    {
        return new App();
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
     * Used to assign a session handler to the application.
     *
     * @param Session $session
     * @return $this
     */
    public function setSession(Session $session)
    {
        $this->session = $session;

        return $this;
    }


    /**
     * Returns the configured session handler.
     *
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }


    /**
     * Used to assign a database definition to the application.
     *
     * @param Schema $schema
     * @return $this
     */
    public function setSchema(Schema $schema)
    {
        $this->schema = $schema;

        return $this;
    }


    /**
     * Returns the configured schema.
     *
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }


    /**
     * Used to assign a logger instance.
     *
     * @param Logger $logger
     * @return $this
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;

        return $this;
    }


    /**
     * Returns the logger instance.
     *
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }


    /**
     * Assigns the debugger.
     *
     * @param Debug $debugger
     * @return $this
     */
    public function setDebugger(Debug $debugger)
    {
        $this->debugger = $debugger;

        return $this;
    }


    /**
     * Returns the debugger.
     *
     * @return Debugger
     */
    public function getDebugger()
    {
        return $this->debugger;
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
     * @return Migrator
     */
    public function getMigrator()
    {
        return $this->migrator;
    }


    /**
     * Used to configure the console instance.
     *
     * @param Console $console
     * @return $this
     */
    public function setConsole(Console $console)
    {
        $this->console = $console;

        return $this;
    }


    /**
     * Returns the assigned console handler.
     *
     * @return Console
     */
    public function getConsole()
    {
        return $this->console;
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


    /**
     * Returns the configured logger.
     *
     * @return Logger
     */
    public static function logger()
    {
        return self::getInstance()->getLogger();
    }


    /**
     * Returns the configured console handler.
     *
     * @return Console
     */
    public static function console()
    {
        return self::getInstance()->getConsole();
    }


    /**
     * Returns the session handler.
     *
     * @return Session
     */
    public static function session()
    {
        return self::getInstance()->getSession();
    }


    /**
     * Returns the configured server handler.
     *
     * @return Server
     */
    public static function server()
    {
        return self::getInstance()->getServer();
    }


    /**
     * Returns the configured router.
     *
     * @return Router
     */
    public static function router()
    {
        return self::getInstance()->getRouter();
    }


    /**
     * Returns the configuration instance.
     *
     * @return Config
     */
    public static function config()
    {
        return self::getInstance()->getConfig();
    }


    /**
     * Returns the migrator instance.
     *
     * @return Migrator
     */
    public static function migrator()
    {
        return self::getInstance()->getMigrator();
    }


    /**
     * Returns the debugger instance.
     *
     * @return Debug
     */
    public static function debugger()
    {
        return self::getInstance()->getDebugger();
    }
}