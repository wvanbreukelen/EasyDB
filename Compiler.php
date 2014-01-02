<?php

namespace EasyDB;

class Compiler
{

	/**
	 * The type to compile
	 */

	protected $type;

	protected $booleans = array('equals' => "=", 'bigger' => ">", 'smaller' => "<");

	protected $types = array("SELECT", "INSERT", "UPDATE", "DELETE");

	protected $selectContains = array("Columns", "From",  "Wheres");

	protected $insertContains = array("Columns", "Values", "From");

	public function compile($type, array $attributes = array())
	{
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
			echo $containment;
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

				$compiled .= $column . $boolean . explode('|', $bo)[1] . SPACE . "AND" . SPACE;
			}
			$compiled = substr($compiled, 0, -4);
		}

		return $compiled;
	}

	protected function compileFrom($table)
	{

		throw new \Exception("Epic fail!");
		$compiled = null;
		if (!is_null($table))
		{
			return $compiled .= SPACE . "FROM" . SPACE . $table . SPACE;
		}
	}

	protected function compileValues(array $values = array())
	{
		$compiled = "(";

		foreach ($values as $id => $value)
		{
			$compiled .= "`" . $id . "`," . SPACE;
		}
		$compiled = substr($compiled, 0, -2);

		$compiled .= ")" . SPACE . "VALUES" . SPACE . "(";

		foreach ($values as $id => $value)
		{
			if (is_int($value))
			{
				$compiled .= $value . "," . SPACE;
			} else {
				$compiled .= "'" . $value . "'," . SPACE;
			}
		}

		$compiled = substr($compiled, 0, -2);

		return $compiled .= ")";
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

	public function setCompileType($type)
	{
		$this->type = $type;
	}

	public function getCompileType()
	{
		return $this->type;
	}
}