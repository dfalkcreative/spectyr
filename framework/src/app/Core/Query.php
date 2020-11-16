<?php

namespace App\Core;

use App\Core\Exception\QueryException;
use Exception;

/**
 * Class Query
 *
 * @package App\Core
 */
class Query
{
    /**
     * The table to reference.
     *
     * @var string
     */
    protected $target = '';


    /**
     * The fields to select.
     *
     * @var array
     */
    protected $select = [];


    /**
     * The conditions.
     *
     * @var array
     */
    protected $conditions = [];


    /**
     * Any ordering parameters.
     *
     * @var array
     */
    protected $orders = [];


    /**
     * An offset amount.
     *
     * @var int
     */
    protected $offset = 0;


    /**
     * A limit amount.
     *
     * @var int
     */
    protected $limit = 0;


    /**
     * Query bindings.
     *
     * @var array
     */
    protected $bindings = [];


    /**
     * Query constructor.
     */
    public function __construct()
    {

    }


    /**
     * Used to configure the table source.
     *
     * @param string $table
     * @return $this
     */
    public function table($table = '')
    {
        $this->target = $table;

        return $this;
    }


    /**
     * Used to append columns for selection.
     *
     * @param array $select
     * @return $this
     */
    public function select($select = [])
    {
        $this->select = $select;

        return $this;
    }


    /**
     * Used to configure the bindings.
     *
     * @param array $bindings
     * @return $this
     */
    public function setBindings($bindings = [])
    {
        $this->bindings = $bindings;

        return $this;
    }


    /**
     * Returns the configured bindings.
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }


    /**
     * Returns the configured target.
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }


    /**
     * Used to register a new binding to the front.
     *
     * @param $value
     * @return $this
     */
    public function prependBinding($value)
    {
        array_unshift($this->bindings, $value);

        return $this;
    }


    /**
     * Used to register a new binding.
     *
     * @param $value
     * @return $this
     */
    public function addBinding($value)
    {
        $this->bindings[] = $value;

        return $this;
    }


    /**
     * Register a new conditional statement.
     *
     * @param $condition
     * @return $this
     */
    public function addCondition($condition)
    {
        $this->conditions[] = $condition;

        return $this;
    }


    /**
     * Used to merge bindings.
     *
     * @param array $bindings
     * @return Query
     */
    public function mergeBindings($bindings = [])
    {
        if (has($bindings)) {
            foreach ($bindings as $value) {
                $this->addBinding($value);
            }
        }

        return $this;
    }


    /**
     * Add an AND condition to the statement.
     *
     * @param $field
     * @param string $expression
     * @param string $value
     * @return Query
     */
    public function where($field, $expression = '=', $value = '')
    {
        $prepend = has($this->conditions) ? 'and' : '';

        if ($field instanceof Query) {
            return $this->addCondition("$prepend ({$field->getJoinedConditions()})")
                ->mergeBindings($field->getBindings());
        }

        return $this->addBinding($value)->addCondition("$prepend $field $expression ?");
    }


    /**
     * Add an OR condition to the statement.
     *
     * @param $field
     * @param string $expression
     * @param string $value
     * @return Query
     */
    public function orWhere($field, $expression = '=', $value = '')
    {
        $prepend = has($this->conditions) ? 'or' : '';

        if ($field instanceof Query) {
            return $this->addCondition("$prepend ({$field->getJoinedConditions()})")
                ->mergeBindings($field->getBindings());
        }

        return $this->addBinding($value)
            ->addCondition("$prepend $field $expression ?");
    }


    /**
     * Add an ordering clause.
     *
     * @param $field
     * @param string $direction
     * @return Query
     */
    public function orderBy($field, $direction = 'asc')
    {
        $this->orders[] = "order by $field $direction";

        return $this;
    }


    /**
     * Used to configure the offset.
     *
     * @param $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }


    /**
     * Used to apply limiting.
     *
     * @param $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }


    /**
     * Returns the merged conditional statement.
     *
     * @return string
     */
    public function getJoinedConditions()
    {
        return trim(implode(' ', $this->conditions));
    }


    /**
     * Used to combine the conditions as one statement.
     *
     * @return string
     */
    public function getConditionalStatement()
    {
        $joined = $this->getJoinedConditions();

        return $joined ? "where $joined" : '';
    }


    /**
     * Returns the joined select statement.
     *
     * @return string
     */
    public function getSelectStatement()
    {
        // Determine which fields to include.
        $fields = has($this->select) ?
            implode(', ', $this->select) : '*';

        // Select the fields.
        return "select {$fields} from {$this->target}";
    }


    /**
     * Returns the joined select statement.
     *
     * @param array $values
     * @return string
     */
    public function getUpdateStatement($values = [])
    {
        $original = $this->getBindings();
        $this->setBindings([]);
        $statement = [];

        foreach ($values as $column => $value) {
            $statement[] = "$column = ?";
            $this->addBinding($value);
        }

        $this->setBindings(array_merge($this->getBindings(), $original));

        // Select the fields.
        $statement = implode(', ', $statement);
        return "update {$this->target} set {$statement}";
    }


    /**
     * Returns the joined ordering statement.
     *
     * @return string
     */
    public function getOrderByStatement()
    {
        return implode(' ', $this->orders);
    }


    /**
     * Returns a joined limit statement.
     *
     * @return string
     */
    public function getLimitStatement()
    {
        return $this->limit ? "limit $this->limit" : '';
    }


    /**
     * Returns a joined offset statement.
     *
     * @return string
     */
    public function getOffsetStatement()
    {
        return $this->limit && $this->offset ? "offset $this->offset" : '';
    }


    /**
     * Returns the full statement.
     *
     * @return string
     */
    public function getStatement()
    {
        $statements = [
            $this->getSelectStatement(),
            $this->getConditionalStatement(),
            $this->getOrderByStatement(),
            $this->getLimitStatement(),
            $this->getOffsetStatement()
        ];

        return trim(implode(' ', $statements));
    }


    /**
     * Used to insert a new record.
     *
     * @param array $values
     * @return bool
     * @throws QueryException
     */
    public function insert($values = [])
    {
        // Verify that we have values to insert.
        if (!has($values)) {
            return false;
        }

        // Build out the appropriate SQL string.
        $columns = implode(', ', array_keys($values));
        $bindings = implode(', ', array_map(function() {
            return '?';
        }, $values));

        try {
            (new Connection())
                ->statement("insert into {$this->target} ($columns) values ($bindings)", array_values($values));

            return true;
        } catch (Exception $e) {
            throw new QueryException($e->getMessage());
        }
    }


    /**
     * Used to perform an update.
     *
     * @param array $values
     * @throws QueryException
     */
    public function update($values = [])
    {
        $statements = [
            $this->getUpdateStatement($values),
            $this->getConditionalStatement()
        ];

        try {
            (new Connection())
                ->statement(trim(implode(' ', $statements)), $this->getBindings());
        } catch (Exception $e) {
            throw new QueryException($e->getMessage());
        }
    }


    /**
     * Used to create a new entry.
     *
     * @param array $values
     * @return array
     * @throws QueryException
     */
    public function create($values = [])
    {
        if ($this->insert($values)) {
            return query()->table($this->target)
                ->orderBy('id', 'desc')
                ->first();
        }

        return [];
    }


    /**
     * Used to retrieve the results.
     *
     * @return array
     * @throws QueryException
     */
    public function get()
    {
        try {
            $collection = [];
            $results = (new Connection())
                ->setQuery($this)
                ->execute();

            if (has($results)) {
                foreach ($results as $result) {
                    $collection[] = $this->instance($result);
                }
            }

            return $collection;
        } catch (Exception $e) {
            throw new QueryException($e->getMessage());
        }
    }


    /**
     * Used to retrieve the results.
     *
     * @return array
     * @throws QueryException
     */
    public function first()
    {
        try {
            $results = (new Connection())
                ->setQuery($this->limit(1))
                ->execute();

            // Return an individual result.
            return has($results) ? $this->instance(array_shift($results)) : [];
        } catch (Exception $e) {
            throw new QueryException($e->getMessage());
        }
    }


    /**
     * Used to map query results.
     *
     * @param array $attributes
     * @return array
     */
    public function instance($attributes = [])
    {
        return $attributes;
    }
}