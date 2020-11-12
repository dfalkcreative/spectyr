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
    protected $table = '';


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
        $this->table = $table;

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
        return "select {$fields} from {$this->table}";
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
     * Used to retrieve the results.
     *
     * @return array
     * @throws QueryException
     */
    public function get()
    {
        try {
            return (new Connection())->setQuery($this)->execute();
        } catch (Exception $e) {
            throw new QueryException($e->getMessage());
        }
    }
}