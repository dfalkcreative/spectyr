<?php

namespace App\Core;

use Exception;
use App\Core\Definition\Table;
use App\Core\Definition\Schema;

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
     *
     * @param Schema $schema
     */
    public function __construct(Schema $schema)
    {
        $this->setSchema($schema);
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

        foreach ($this->getSchema()->getTables() as $table => $data) {
            $this->migrateTable($this->getSchema()->getTable($table));
        }
    }


    /**
     * Returns a connection instance for the schema.
     *
     * @return Connection
     */
    public function getConnectionInstance()
    {
        return (new Connection())
            ->setHost(env(App::ENVIRONMENT_DB_HOST, $this->getSchema()->getHost()))
            ->setUser(env(App::ENVIRONMENT_DB_USER, $this->getSchema()->getUser()))
            ->setPassword(env(App::ENVIRONMENT_DB_PASS, $this->getSchema()->getPassword()))
            ->setDatabase(env(App::ENVIRONMENT_DB_NAME, $this->getSchema()->getDatabase()));
    }


    /**
     * Returns the columns for a particular table.
     *
     * @param $table
     * @return array
     * @throws Exception
     */
    public function getTableColumns($table)
    {
        return ($connection = $this->getConnectionInstance())
            ->setQuery(query()
                ->select(['column_name', 'data_type'])
                ->table('information_schema.columns')
                ->where('table_schema', '=', $connection->getDatabase())
                ->where('table_name', '=', $table))
            ->execute();
    }


    /**
     * Used to create the database if it doesn't exist.
     */
    public function createSchemaIfNotExists()
    {
        // Verify that the connection was successful.
        if (!$connection = mysqli_connect(
            env(App::ENVIRONMENT_DB_HOST, $this->getSchema()->getHost()),
            env(App::ENVIRONMENT_DB_USER, $this->getSchema()->getUser()),
            env(App::ENVIRONMENT_DB_PASS, $this->getSchema()->getPassword())
        )) {
            return;
        }

        // Attempt to build the database if it doesn't exist.
        $database = env(App::ENVIRONMENT_DB_NAME, $this->getSchema()->getDatabase());

        if (!mysqli_select_db($connection, $database)) {
            mysqli_query($connection, "create database {$database}");
        }

        // Close the connection.
        mysqli_close($connection);
    }


    /**
     * Used to migrate an individual table.
     *
     * @param Table $table
     */
    public function migrateTable(Table $table)
    {
        $connection = $this->getConnectionInstance();

        try {
            if (!has($results = $this->getTableColumns($table->getTable()))) {
                $statement = [
                    "id int primary key auto_increment",
                    "created_at datetime",
                    "updated_at datetime"
                ];

                // Build out the statement for each column.
                foreach ($table->getColumns() as $key => $column) {
                    $column = $table->getColumn($key);
                    $statement[] = "$key {$column->getSqlType()}";
                }

                // Execute the table creation statement.
                $statement = implode(', ', $statement);
                $connection->statement("create table {$table->getTable()} ($statement);");
                return;
            }

            // Used to record results.
            $statement = [];
            $information = [];

            // Map the results into an easier format to navigate.
            foreach ($results as $result) {
                $type = array_get($result, 'data_type');
                $type = 'varchar' ? 'varchar(191)' : $type;

                // Record the column type.
                $information[array_get($result, 'column_name')] = $type;
            }

            foreach ($table->getColumns() as $key => $column) {
                $column = $table->getColumn($key);

                // Create the column if it doesn't exist.
                if (!array_key_exists($key, $information)) {
                    $statement[] = "alter table {$table->getTable()} add $key {$column->getSqlType()};";
                    continue;
                }

                // Modify the column if the type has been modified.
                if ($information[$key] != $column->getSqlType()) {
                    $statement[] = "alter table {$table->getTable()} modify $key {$column->getSqlType()};";
                }
            }

            // Determine whether or not we have an alter statement to make.
            if (has($statement)) {
                foreach ($statement as $action) {
                    $connection->statement($action);
                }
            }
        } catch (Exception $e) {
            return;
        }
    }
}