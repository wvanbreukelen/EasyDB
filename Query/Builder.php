<?php

namespace EasyDB\Query;

use EasyDB\Connection;

class Builder
{

	protected $booleans = array('equals' => "=", 'bigger' => ">", 'smaller' => "<");

	protected $types = array("SELECT", "INSERT", "UPDATE", "DELETE");

	protected $type;

	protected $connection;

	protected $grammar;

	protected $table;

	protected $columns;

	protected $wheres;

	public function __construct(Connection $connection, $table = null)
	{
		$this->connection = $connection;
		//$this->grammar = $grammar;
		$this->table = $table;
	}

	public function select($columns = array('*'))
	{
		$this->columns = is_array($columns) ? $columns : func_get_args();
		$this->setType("SELECT");

		return $this;
	}

	public function where($column, $operator = null, $boolean = 'equals')
	{
		$this->wheres[$column] = $boolean . '|' . $operator;

		return $this;
	}

	public function build()
	{
		$query = $this->getType() . SPACE;

		$query .= $this->compileColumns($this->columns);
		$query .= $this->compileFrom($this->table);
		$query .= $this->compileWheres($this->wheres);

		$this->connection->execute($query);
	}

	protected function setType($type)
	{
		if (in_array($type, $this->types))
		{
			$this->type = $type;
		} else {
			throw new \Exception("Type " . $type . " is not supported!");
		}
	}

	protected function getType()
	{
		return $this->type;
	}

	protected function compileColumns(array $columns = array())
	{
		$compiled = null;
		if (count($columns > 0))
		{
			foreach ($columns as $column)
			{
				$compiled .= $column . ",";
			}
			$compiled = trim($compiled, ",") . SPACE;
		}

		return $compiled;
	}

	protected function compileWheres(array $wheres = array())
	{
		$compiled = null;
		if (count($wheres) > 0)
		{
			$compiled .= "WHERE" . SPACE;

			foreach ($this->wheres as $column => $bo)
			{
				$boolean = $this->convertBool(explode('|', $bo)[0]);

				$compiled .= $column . $boolean . explode('|', $bo)[1] . SPACE . "AND" . SPACE;
			}
			$compiled = substr($compiled, 0, -4);
		}

		return $compiled;
	}

	protected function compileFrom($table)
	{
		$compiled = null;
		if (!is_null($table))
		{
			return $compiled .= SPACE . "FROM" . SPACE . $table . SPACE;
		}
	}

	protected function convertBool($boolean)
	{
		if (is_string($boolean))
		{
			if (isset($this->booleans[$boolean]))
			{
				return $this->booleans[$boolean];
			} else {
				throw new \Exception("QueryBuilder: Boolean " . $boolean . " is not supported!");
			}
		} else {
			throw new \Exception("QueryBuilder: Cannot convert boolean, because the variable type is " . gettype($boolean) . " and it must be a string!");
		}

		return;
	}
}