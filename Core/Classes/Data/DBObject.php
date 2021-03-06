<?php
namespace Core\Classes\Data;
use Core\Classes as CoreClasses;
use \PDO;
/** 
* 	Provides a PDO DB connection to instances of dbobject and its descendants
*	@author Jason Savell <jsavell@library.tamu.edu>
*
*/

class db {
	public $handle;
	private static $instance;

    private function __construct() {
		$this->handle = new PDO($GLOBALS['config']['DB_DSN'], $GLOBALS['config']['DB_USER'], $GLOBALS['config']['DB_PASSWORD']);
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
*	Provides a DB connection instance and several abstractions for DB interaction
*	@author Jason Savell <jsavell@library.tamu.edu>
*
*	@todo Make abstract and leave implementing of relevant methods to specific implementers (MySqlObject, MsSqlObject)
*	@todo Provide getter/setter for $primaryTable to be utilized by extending classes
*/
class DBObject extends CoreClasses\CoreObject {
	/** @var db An instance of the db class, providing the connection to the DB */
	protected $db;
	/** @var string The name of the main db table associated with an instance of DBObject */
	protected $primaryTable;
	
	/**
	*	Gets a db instance and assigns it to the $db property
	*/
	protected function __construct() {
		$this->db = db::getInstance();
	}

	/**
	*	Returns an appropriate date format function for the SQL language
	*	@return string $sql
	*/
	protected function dbFormatDate($field) {
		if (strtolower($GLOBALS['dbconfig']['dbtype']) == 'mssql') {
			$sql = "CONVERT(VARCHAR(10), {$field},101)";
		} else {
			$sql = "CAST({$field} AS CHAR(10))";
		}
		return $sql;
	}

	/**
	*	Returns an appropriate text search function for the SQL language
	*	@return string $sql
	*/
	protected function dbTextMatch($fields,$value) {
		if (strtolower($GLOBALS['dbconfig']['dbtype']) == strtolower('mssql')) {
			$sql = "FREETEXT(({$fields}),{$value})";
		} else {
			$sql = "MATCH({$fields}) AGAINST({$value})";
		}
		return $sql;
	}

	/**
	*	Returns an appropriate CURRENT TIME function for the SQL language
	*	@return string $sql
	*/
	protected function dbNow() {
		if ( strtolower($GLOBALS['dbconfig']['dbtype']) == strtolower('mssql')) {
			$sql = "GETDATE()";
		} else {
			$sql = "NOW()";
		}
		return $sql;
	}

	/** 
	*	Execute a query and return the results as an array
	* 	@param string $sql the SQL query
	*  	@param mixed[] $bindparams: an array of values to be binded by PDO to any query parameters
	*	@return array[]|false $results A two dimensional array representing the resulting rows: array(array("id"=>1,"field"=>"value1"),array("id"=>2","field"=>"value2")), false on failure
	*/
	protected function executeQuery($sql,$bindparams=NULL) {
		$result = $this->db->handle->prepare($sql);
		$result->execute($bindparams);
		if ($result->errorCode() == '00000') {
			return $result->fetchAll(PDO::FETCH_ASSOC);
		} 
		$this->logStatementError($result->errorInfo(),$sql);
		return false;
	}

	/** 
	*	Execute an update query
	* 	@param string $sql The SQL query
	*  	@param mixed[] $bindparams An array of values to be binded by PDO to any query parameters
	*	@return boolean True on success, false on anything else
	*/
	protected function executeUpdate($sql,$bindparams=NULL) {
		$result = $this->db->handle->prepare($sql);
		$result->execute($bindparams);
		if ($result->errorCode() == '00000') {
			return true;
		} 
		$this->logStatementError($result->errorInfo(),$sql);
		return false;
	}

	/**
	*	Returns the id of the most recent insert query
	*	@return int The id of the last inserted record
	*/

	protected function getLastInsertId() {
		return $this->db->handle->lastInsertId();
	}

	/**  
	*	Query the DB and return the rows as a 1 or 2 dimensional indexed array
	*	@param string $sql The query string
	*   @param string $index The table's primary key
	*	@param string $findex An optional foreign key from the table (when used, returns a 2 dimensional array, indexed first by $index, second by $findex)
	*  	@param mixed[] $bindparams An array of values to be binded by PDO to any query parameters
	*	@return array[]|false $results A two dimensional array representing the resulting rows: array(array("id"=>1,"field"=>"value1"),array("id"=>2","field"=>"value2")), false on failure
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
	
	/** 
	*	Escape a @value to prep for use in a DB query
	*	@return string The escaped $value
	*/
	protected function quote($value) {
		return $this->db->handle->quote($value);
	}

	/**
	*	Escapes the contents of an array and returns the result
	*	@return string[] The escaped array
	*/
	protected function quoteArray($ar) {
		return array_map(array($this,"quote"),$ar);
	}

	/** 
	*	Returns a parametrized IN clause for use in a prepared statement
	*	@param mixed[] $ar An array of values representing the contents of the IN clause
	*	@param mixed[] $bindparams A reference to the caller's array of binded parameters
	*	@param string $varprefix Can be used to avoid bind parameter naming collisions when calling multiple times within 1 statement
	*	@return string The resulting IN clause
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

	/**
	*	Builds and executes an insert statement
	*	@param mixed[] $data An associative array (ColumnName->Value) of data representing the new DB record
	*	@param string $table Optional - The table to insert the new record into. Defaults to $primaryTable
	*	@return int/false Returns the ID of the new record on success, false on failure
	*/
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

	/**
	*	Builds and executes a single insert statement that inserts multiple new records
	*	@param mixed[][] $rows An array of associative arrays (ColumnName->Value) of data representing the new DB records
	*	@param string $table Optional - The table to insert the new records into. Defaults to $primaryTable
	*	@return boolean True on success, false on failure
	*/
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

	/**
	*	Builds and executes an update statement
	*	@param int $id The id of the record to be updated
	*	@param mixed[] $data An associative array (ColumnName->Value) of data representing the updated data
	*	@param string $table Optional - The table to insert the new record into. Defaults to $primaryTable
	*	@return boolean True on success, false on failure
	*/
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

	/**
	* 	Logs SQL errors to the logger
	*	@param array $error A PDO::errorInfo() error
	*	@param string $sql The SQL query that triggered the error
	*/
	protected function logStatementError($error,$sql=null) {
		if (!empty($GLOBALS['config']['DB_DEBUG'])) {
			$message = "Error with query - CODE: {$error[1]}";
			if ($sql) {
				$message .= " QUERY: {$sql}";
			}
			$this->getLogger()->error($message);
		}
	}
}
?>