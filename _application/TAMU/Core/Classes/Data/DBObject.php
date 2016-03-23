<?php
namespace TAMU\Core\Classes\Data;
use \PDO;
/** 
* 	Provides a PDO DB connection to instances of dbobject and its descendants
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class db {
	public $handle;
	private static $instance;

    private function __construct() {
		try {
        	$this->handle = new PDO($GLOBALS['config']['DB_DSN'], $GLOBALS['config']['DB_USER'], $GLOBALS['config']['DB_PASSWORD']);
		} catch (PDOException $e) {
			if ($GLOBALS['DB_DEBUG']) {
				echo '<pre>';
				print_r($e);
				echo '</pre>';
			}
			throw $e;
		}
    }

    public static function getInstance() {
        if (!isset(self::$instance)) {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }
}

/** 
*	A base class to be extended by any class that needs to access the DB
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class DBObject {
	protected $db;
	protected $primaryTable;
	
	protected function __construct() {
		//get the DB connection
		$this->db = db::getInstance();
	}

	//returns an appropriate date format function for the query language
	protected function dbFormatDate($field) {
		if (strtolower($GLOBALS['dbconfig']['dbtype']) == 'mssql') {
			$sql = "CONVERT(VARCHAR(10), {$field},101)";
		} else {
			$sql = "CAST({$field} AS CHAR(10))";
		}
		return $sql;
	}

	//returns an appropriate text search function for the query language
	protected function dbTextMatch($fields,$value) {
		if (strtolower($GLOBALS['dbconfig']['dbtype']) == strtolower('mssql')) {
			$sql = "FREETEXT(({$fields}),{$value})";
		} else {
			$sql = "MATCH({$fields}) AGAINST({$value})";
		}
		return $sql;
	}

	//returns the current time function for the query language
	protected function dbNow() {
		if ( strtolower($GLOBALS['dbconfig']['dbtype']) == strtolower('mssql')) {
			$sql = "GETDATE()";
		} else {
			$sql = "NOW()";
		}
		return $sql;
	}

	/** execute a query and return the results as an array
	*  @sql: the query
	*  @bindparams: an array of values to be binded to any query parameters
	*/
	protected function executeQuery($sql,$bindparams=NULL) {
		$result = $this->db->handle->prepare($sql);
		if ($result->execute($bindparams)) {
			return $result->fetchAll(PDO::FETCH_ASSOC);
		} elseif (isset($GLOBALS['debugDb']) && $GLOBALS['debugDb']) {
			print_r($result->errorInfo());
		}
		return false;
	}

	protected function executeUpdate($sql,$bindparams=NULL) {
		$result = $this->db->handle->prepare($sql);
		if ($result->execute($bindparams)) {
			return true;
		} elseif (isset($GLOBALS['debugDb']) && $GLOBALS['debugDb']) {
			print_r($result->errorInfo());
		}
		return false;
	}

	protected function getLastInsertId() {
		return $this->db->handle->lastInsertId();
	}

	/**  query the DB and return the rows as a 1 or 2 dimensional indexed array
	*	@sql: the query
	*   @index: the table's primary key
	*	@findex: an optional foreign key from the table (when used, returns a 2 dimensional array)
	*/

	protected function queryWithIndex($sql,$index,$findex=NULL,$bindparams=NULL) {
		if ($result = $this->executeQuery($sql,$bindparams)) {
			$temp = array();
			if ($findex) {
				foreach ($result as $row) {
					$temp[$row[$findex]][$row[$index]] = $row;
				}
			} else {
				foreach ($result as $row) {
					$temp[$row[$index]] = $row;
				}
			}
			return $temp;
		}
		return false;
	}
	
	/** escape a @value to prep for use in a DB query
	*/
	protected function quote($value) {
		return $this->db->handle->quote($value);
	}

	/**cleans an @ar and returns the result
	*/
	protected function quoteArray($ar) {
		return array_map(array($this,"quote"),$ar);
	}

	/** Returns a parametrized IN clause for use in a prepared statement
	* @ar: an array of values for IN
	* &@bindparams: a reference to the caller's array of binded parameters
	* @varprefix: use to avoid naming collisions when calling multiple times within 1 statement
	*/
	protected function buildIn($ar,&$bindparams,$varprefix = 'v') {
		$x=1;
		foreach ($ar as $value) {
			$sql .= ":{$varprefix}{$x},";
			$bindparams[":{$varprefix}{$x}"] = $value;
			$x++;
		}
		return 'IN ('.rtrim($sql,',').')';
	}

	protected function buildInsertStatement($data,$table=null) {
		if (!$table) {
			$table = $this->primaryTable;
		}
		$bindparams = array();
		$sql_fields = NULL;
		$sql_values = NULL;
		foreach ($data as $field=>$value) {
			$sql_fields .= "{$field},";
			$sql_values .= ":{$field},";
			$bindparams[":{$field}"] = $value;
		}
		$sql_fields = rtrim($sql_fields,',');
		$sql_values = rtrim($sql_values,',');
		$sql = "INSERT INTO {$table} ({$sql_fields}) VALUES ({$sql_values})";
		if ($this->executeUpdate($sql,$bindparams)) {
			return $this->getLastInsertId();
		}
		return false;
	}

	protected function buildMultiRowInsertStatement($rows,$table=null) {
		if (!$table) {
			$table = $this->primaryTable;
		}
		$bindparams = array();
		$sqlRows = NULL;
		$sqlFields = implode(',',array_keys(current($rows)));
		$x = 1;
		foreach ($rows as $data) {
			$sqlValues = NULL;
			foreach ($data as $field=>$value) {
				$sqlValues .= ":{$field}{$x},";
				$bindparams[":{$field}{$x}"] = $value;
			}
			$sqlValues = rtrim($sqlValues,',');
			$sqlRows .= "({$sqlValues}),";
			$x++;
		}
		$sql = "INSERT INTO {$table} ({$sqlFields}) VALUES ".rtrim($sqlRows,',');
		return $this->executeUpdate($sql,$bindparams);
	}

	protected function buildUpdateStatement($id,$data,$table=null) {
		if (!$table) {
			$table = $this->primaryTable;
		}
		$sql = "UPDATE {$table} SET ";
		foreach ($data as $field=>$value) {
			$sql .= "{$field}=:{$field},";
			$bindparams[":{$field}"] = $value;
		}
		
		$sql = rtrim($sql,',')." WHERE id=:id";
		$bindparams[":id"] = $id;
		if ($this->executeUpdate($sql,$bindparams)) {
			return true;
		}
		return false;
	}
}
?>