<?php

namespace db;

include 'config.php';

use const config\HOST;
use const config\DB;
use const config\USER;
use const config\PWD;
use PDO;
use PDOException;

class Statement {

	/**
	 * The PDO object
	 * 
	 * @var \PDO
	 */
	private PDO $pdo;

	/**
	 * The pointer to the singleton class object
	 * 
	 * @var db\Statement
	 */
	private static self $ref;

	/**
	 * Mainly initializes the PDO object
	 * 
	 * @throws \PDOException
	 */
	private function __construct() {

		$this->pdo = new PDO('mysql:host=' . HOST . ';dbname=' . DB, USER, PWD);

	}

	/**
	 * Singleton method
	 * 
	 * If no object were created, create
	 * a new, then returns.
	 * 
	 * @throws \PDOException
	 * @return db\Statement self::$ref
	 */
	public static function getInstance() : self {

		if (empty (self::$ref))
			self::$ref = new self;

		return self::$ref;

	}

	/**
	 * Get a set of rows
	 * 
	 * Return the all row from a SQL Statement, or
	 * null if some error occur. The debug properties
	 * are set in this case.
	 * 
	 * @param string     $query     The SQL query string
	 * @param array|null $bindings  Bindings to be bound on PDOStatement::bindValue()
	 * 
	 * @throws \PDOException
	 * @return array|null
	 */
	public function getRows(string $query, ?array $bindings = null) : ?array {

		$s = $this->pdo->prepare($query);

		if ($bindings)
			foreach ($bindings as $key => $binding) {

				$value = $binding;
				$type = PDO::PARAM_STR;

				if (is_array($binding)) {

					$value = $binding[0];
					$type = $binding[1];

				}

				$s->bindParam($key + 1, $value, $type);

			}

		$s->execute();

		return $s->fetchAll(PDO::FETCH_ASSOC);

	}

}
