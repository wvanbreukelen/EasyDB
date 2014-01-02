<?php

namespace EasyDB\Query;

use EasyDB\Connection;
use EasyDB\Query\Compiler;

class Builder
{

	protected $booleans = array('equals' => "=", 'bigger' => ">", 'smaller' => "<");

	protected $types = array("SELECT", "INSERT", "UPDATE", "DELETE");

	protected $type = null;

	protected $connection;

	protected $compiler;

	protected $grammar;

	protected $from = null;

	protected $into = null;

	protected $columns = null;

	protected $wheres = null;

	protected $values = null;

	protected $set = null;

	protected $queryParts = array("wheres", "columns", "from", "values", "into", "set");

	public function __construct(Connection $connection, Compiler $compiler, $table)
	{
		$this->connection = $connection;
		$this->compiler = $compiler;
		$this->from = $table;
		$this->into = $table;
		$this->set = $table;
	}

	public function select($columns = array('*'))
	{
		$this->columns = is_array($columns) ? $columns : func_get_args();
		$this->setType("SELECT");

		return $this;
	}

	public function insert(array $columns = array())
	{
		$this->columns = $columns;
		$this->setType("INSERT");

		return $this;
	}

	public function update(array $columns = array())
	{
		$this->columns = $columns;
		$this->setType("UPDATE");

		return $this;
	}

	public function delete(array $columns = array())
	{
		$this->columns = $columns;
		$this->setType("DELETE");

		return $this;
	}

	public function where($column, $operator = null, $boolean = 'equals')
	{
		$this->wheres[$column] = $boolean . '|' . $operator;

		return $this;
	}

	public function values(array $values = array())
	{
		$this->values = $values;

		return $this;
	}

	public function build()
	{
		$compilerArray = $this->buildCompilerArray();
		
		$query = $this->compiler->compile($this->getType(), $compilerArray);

		$this->connection->execute($query);
	}

	public function buildCompilerArray()
	{
		$result = array();

		foreach ($this->queryParts as $part)
		{
			if (!is_null($part))
			{
				$result[ucfirst(strtolower($part))] = $this->$part;
			}
		}

		return $result;
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
}