<?php

namespace EasyDB\Query;

class Compiler
{

	/**
	 * The type to compile
	 */

	protected $type;

	protected $booleans = array('equals' => "=", 'bigger' => ">", 'smaller' => "<");

	protected $types = array("SELECT", "INSERT", "UPDATE", "DELETE");

	protected $selectContains = array("Columns", "From",  "Wheres");

	protected $insertContains = array("Columns", "Into", "Values");

	protected $updateContains = array("Columns", "Set", "Values", "Where");

	protected $deleteContains = array("Columns", "From", "Wheres");

	public function compile($type, array $attributes = array())
	{
		print_r($attributes);
		return strtoupper($type) . SPACE . $this->{"compile{$type}"}($attributes);
	}

	public function compileSelect(array $attributes = array())
	{
		$query = null;

		foreach ($this->selectContains as $containment)
		{
			if (isset($attributes[$containment]))
			{
				$query .= $this->{"compile{$containment}"}($attributes[$containment]);
			}
		}

		return $query;
	}

	public function compileInsert(array $attributes = array())
	{
		$query = null;

		foreach ($this->insertContains as $containment)
		{
			if (isset($attributes[$containment]))
			{
				$query .= $this->{"compile{$containment}"}($attributes[$containment]);
			}
		}

		return "INTO" . SPACE . $query;
	}

	public function compileUpdate(array $attributes = array())
	{
		$query = null;

		foreach ($this->updateContains as $containment)
		{
			if (isset($attributes[$containment]))
			{
				$query .= $this->{"compile{$containment}"}($attributes[$containment]);
			}
		}

		return SPACE . $query;
	}

	public function compileDelete(array $attributes = array())
	{
		$query = null;

		foreach ($this->deleteContains as $containment)
		{
			if (isset($attributes[$containment]))
			{
				$query .= $this->{"compile{$containment}"}($attributes[$containment]);
			}
		}

		return "INTO" . SPACE . $query;
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

			foreach ($wheres as $column => $bo)
			{
				$boolean = $this->convertBool(explode('|', $bo)[0]);

				$compiled .= $column . $boolean . ":" . $column . "||" . $column . "->" . explode('|', $bo)[1] . SPACE . "AND" . SPACE;
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

	protected function compileInto($table)
	{
		$compiled = null;

		if (!is_null($table))
		{
			return $compiled .= SPACE . $table . SPACE;
		}
	}

	protected function compileValues(array $values = array())
	{
		$preparedQuery = "";
		$parameters = null;

		$preparedQuery .= SPACE . "VALUES" . SPACE . "(";

		foreach ($values as $id => $value)
		{
			$parameters .= $id . "->" . $value . ",";
			$preparedQuery .= ":" . $id . "," . SPACE;
		}

		$preparedQuery = substr($preparedQuery, 0, -2);
		$parameters = substr($parameters, 0, -1);

		return $preparedQuery . ")" . "||" . $parameters;
	}

	protected function compileSet($table)
	{
		$compiled = null;

		if (!is_null($table))
		{
			return $compiled .= SPACE . $table . SPACE . "SET";
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
				throw new \Exception("Query compiler: Boolean " . $boolean . " is not supported!");
			}
		} else {
			throw new \Exception("Query compiler: Cannot convert boolean, because the variable type is " . gettype($boolean) . " and it must be a string!");
		}

		return;
	}

	public function setCompileType($type)
	{
		$this->type = $type;
	}

	public function getCompileType()
	{
		return $this->type;
	}
}