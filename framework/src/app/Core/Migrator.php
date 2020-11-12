<?php

namespace App\Core;

use App\Core\Definition\Schema;
use Exception;

/**
 * Class Migrator
 *
 * @package App\Core
 */
class Migrator
{
    /**
     * The schema object.
     *
     * @var
     */
    protected $schema;


    /**
     * Migrator constructor.
     */
    public function __construct()
    {

    }


    /**
     * Used to configure the schema to reference.
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
     * Builds the database based on a certain schema definition.
     */
    public function build()
    {
        $this->createSchemaIfNotExists();

        foreach($this->getSchema()->getTables() as $table => $data){
            $table = $this->getSchema()->getTable($table);

            $connection = (new Connection())
                ->setHost(env('DB_HOST', $this->getSchema()->getHost()))
                ->setUser(env('DB_USER', $this->getSchema()->getUser()))
                ->setPassword(env('DB_PASS', $this->getSchema()->getPassword()))
                ->setDatabase(env('DB_NAME', $this->getSchema()->getDatabase()));

            $results = $connection->setQuery(query()
                ->select(['COLUMN_NAME'])
                ->table('INFORMATION_SCHEMA.COLUMNS')
                ->where('TABLE_SCHEMA', '=', $connection->getDatabase())
                ->where('TABLE_NAME', '=', $table->getTable()))
            ->execute();

            if(!has($results)){
                $statement = [
                    "id int primary key auto_increment",
                    "created_at datetime",
                    "updated_at datetime"
                ];

                foreach($table->getColumns() as $column => $data){
                    $column = $table->getColumn($column);
                }

                $statement = implode(', ', $statement);
                $connection->statement("CREATE TABLE {$table->getTable()} ($statement);");
            }
        }
    }


    /**
     * Used to create the database if it doesn't exist.
     */
    public function createSchemaIfNotExists()
    {
        // Connect to MySQL
        $connection = mysqli_connect(
            env('DB_HOST', $this->getSchema()->getHost()),
            env('DB_USER', $this->getSchema()->getUser()),
            env('DB_PASS', $this->getSchema()->getPassword())
        );

        // Verify that the connection was successful.
        if (!$connection) {
            return;
        }

        // Attempt to build the database if it doesn't exist.
        $database = env('DB_NAME', $this->getSchema()->getDatabase());

        if (!mysqli_select_db($connection, $database)) {
            mysqli_query($connection, "create database {$database}");
        }

        // Close the connection.
        mysqli_close($connection);
    }
}