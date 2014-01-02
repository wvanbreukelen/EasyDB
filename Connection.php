<?php

namespace EasyDB;

use PDO;

class Connection
{

	protected $pdo;

	protected $database;

	protected $tablePrefix = '';

	public function __construct(PDO $pdo, $database = '', $tablePrefix = '')
	{
		$this->pdo = $pdo;

		$this->database = $database;

		$this->tablePrefix = $tablePrefix;
	}

	public function execute($query)
	{
		if ($this->isCompiled($query))
		{
			$query = $this->convertQuery($query);
			print_r($query);

			try 
			{
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$stmt = $this->pdo->prepare($query['query']);
				
				//$stmt->execute($query['bindings']);
			} catch (PDOException $e) {
				echo 'Error: ' . $e->getMessage();
			}
		}
	}

	public function destroy()
	{
		
	}

	/**
	 * Checks if the query is just 'normal' or it is a compiled query by the query compiler
	 */

	protected function isCompiled($query)
	{
		if (count(explode('||', $query)) > 1)
		{
			return true;
		}
		return false;
	}

	protected function convertQuery($query)
	{
		$pieces = explode('||', $query);

		$query = $pieces[0]; 
		$bindings = explode(",", $pieces[1]);

		$parameters = array();

		foreach ($bindings as $binding)
		{
			$pieces = explode("->", $binding);

			$parameters[":" . $pieces[0]] = $pieces[1];
		}

		return array('query' => $query, 'bindings' => $parameters);
	}
}