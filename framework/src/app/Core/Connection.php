<?php

namespace App\Core;

use PDO;
use Exception;

/**
 * Class Connection
 *
 * @package App\Core
 */
class Connection
{
    /**
     * The host address.
     *
     * @var string
     */
    protected $host;


    /**
     * The database to connect to.
     *
     * @var string
     */
    protected $database;


    /**
     * The user to authenticate against.
     *
     * @var string
     */
    protected $user;


    /**
     * The password to authenticate against.
     *
     * @var string
     */
    protected $password;


    /**
     * The schema character set.
     *
     * @var string
     */
    protected $charset;


    /**
     * The configured query instance to execute.
     *
     * @var
     */
    protected $query;


    /**
     * Connection constructor.
     */
    public function __construct()
    {
        $this->setUser(env(App::ENVIRONMENT_DB_USER, 'root'))
            ->setHost(env(App::ENVIRONMENT_DB_HOST, 'localhost'))
            ->setCharset(env(App::ENVIRONMENT_DB_CHARSET, 'utf8mb4'))
            ->setDatabase(env(App::ENVIRONMENT_DB_NAME, 'master'))
            ->setPassword(env(App::ENVIRONMENT_DB_PASS, ''));
    }


    /**
     * Returns the connection host.
     *
     * @param string $host
     * @return $this
     */
    public function setHost($host = '')
    {
        $this->host = $host;

        return $this;
    }


    /**
     * Returns the connection host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }


    /**
     * Configures the appropriate database.
     *
     * @param string $database
     * @return $this
     */
    public function setDatabase($database = '')
    {
        $this->database = $database;

        return $this;
    }


    /**
     * Returns the database.
     *
     * @return string
     */
    public function getDatabase()
    {
        return $this->database;
    }


    /**
     * Used to configure the user.
     *
     * @param string $user
     * @return $this
     */
    public function setUser($user = '')
    {
        $this->user = $user;

        return $this;
    }


    /**
     * Returns the configured user.
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * Used to configure the password for connecting.
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password = '')
    {
        $this->password = $password;

        return $this;
    }


    /**
     * Returns the connection password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }


    /**
     * Configures the character set.
     *
     * @param string $charset
     * @return $this
     */
    public function setCharset($charset = '')
    {
        $this->charset = $charset;

        return $this;
    }


    /**
     * Returns the configured character set.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }


    /**
     * Used to configure the appropriate query.
     *
     * @param Query $query
     * @return $this
     */
    public function setQuery(Query $query)
    {
        $this->query = $query;

        return $this;
    }


    /**
     * Returns the configured query.
     *
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }


    /**
     * Returns the connection string for transactions.
     *
     * @return string
     */
    public function getConnectionString()
    {
        return "mysql:host={$this->getHost()};dbname={$this->getDatabase()};charset={$this->getCharset()}";
    }


    /**
     * Returns connection arguments.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
    }


    /**
     * Returns an instance of the PDO wrapper.
     *
     * @return PDO
     */
    public function getInstance()
    {
        return new PDO(
            $this->getConnectionString(),
            $this->getUser(),
            $this->getPassword(),
            $this->getOptions()
        );
    }


    /**
     * Executes a SQL statement.
     *
     * @param $statement
     * @param null $bindings
     * @return array
     * @throws Exception
     */
    public function statement($statement, $bindings = null){
        $results = [];

        // Build the connection and execute.
        $statement = $this->getInstance()->prepare($statement);

        if ($statement->execute($bindings) === false) {
            throw new Exception($statement->errorInfo());
        }

        while ($row = $statement->fetch()) {
            $results[] = $row;
        }

        return $results;
    }


    /**
     * Used to execute a query.
     *
     * @return array
     * @throws \Exception
     */
    public function execute()
    {
        if (!$this->getQuery()) {
            return [];
        }

        return $this->statement(
            $this->getQuery()->getStatement(),
            $this->getQuery()->getBindings()
        );
    }
}