<?php
namespace Core\Classes\Data;
use Core\Interfaces as Interfaces;
/** 
*	An abstract implementation of the DataBaseRepository interface
*	Extending this provides CRUD interaction with the configured database table
*
*	@todo Provide another layer to allow for non MySQL flavors of SQL
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

abstract class AbstractDataBaseRepository extends DBObject implements Interfaces\DataRepository {
	/** @var Interfaces\Site $site This provides the Site context to all DatabaseRepositories extending this class */
	protected $site;
	/** @var string $primaryTable This is the name of the DB table managed by DatabaseRepositories extending this class */
	protected $primaryTable;
	/** @var string $primaryKey This is the name of the Primary Key for the $primaryTable managed by DatabaseRepositories extending this class */
	protected $primaryKey = 'id';
	/** @var string $defaultOrderBy If provided, AbstractDataBaseRepository::get()) will ORDER BY this property. */
	protected $defaultOrderBy;
	/** @var string[] $gettableColumns If provided, AbstractDataBaseRepository::get()) will only SELECT the columns present in this array. */
	protected $gettableColumns;
	/** @var string[] $searchableColumns If provided, AbstractDataBaseRepository::search()) will carry out its search on these columns */
	protected $searchableColumns;

	/**
	*	Extending classes configure themselves using this constructor.
	*
	*	@param string $primaryTable Required. This specializes an instance of an extending class to the given DB table name
	*	@param string $primaryKey Required. Extending classes define the Primary Key of the table they manage
	*	@param string $defaultOrderBy Optional. Explicitly define a column to order query results by
	*	@param string[] $gettableColumns Optional. AbstractDataBaseRepository::get()) will SELECT only these fields, when passed
	*	@param string[] $searchableColumns Optional. AbstractDataBaseRepository::search()) will search these columns
	*
	*/
	protected function __construct($primaryTable,$primaryKey,$defaultOrderBy=null,$gettableColumns=null,$searchableColumns=null) {
		$this->primaryTable = $primaryTable;
		$this->primaryKey = $primaryKey;
		$this->defaultOrderBy = $defaultOrderBy;
		$this->gettableColumns = $gettableColumns;
		$this->searchableColumns = $searchableColumns;
		parent::__construct();
	}

	/**
	*	Get all rows from the $primaryTable, optionally ordered by $defaultOrderBy, with selected columns optionally limited to $gettableColumns
	*
	*	@return array[] $results A two dimensional array representing the resulting rows: array(array("id"=>1,"field"=>"value1"),array("id"=>2","field"=>"value2"))
	*/
	public function get() {
		$sql = "SELECT ".(($this->gettableColumns) ? "{$this->primaryKey},".implode(",",$this->gettableColumns):"*")." FROM {$this->primaryTable}";
		if ($this->defaultOrderBy) {
			$sql .= " ORDER BY {$this->defaultOrderBy}";
		}
		return $this->queryWithIndex($sql,$this->primaryKey);
	}

	/**
	*	Get all rows from the $primaryTable matching the search %$term% against a 'name' field
	*	
	*	@param string $term The search criteria
	*	@return array[] $results A two dimensional array representing the resulting rows: array(array("id"=>1,"field"=>"value1"),array("id"=>2","field"=>"value2"))
	*/
	public function search($term) {
		if ($this->getSearchableColumns()) {
			$sql = "SELECT * FROM {$this->primaryTable} WHERE ";

			$searchColumns = $this->getSearchableColumns();
			$bindParams = array();
			$columnCount = count($searchColumns);
			for ($x=0;$x<$columnCount;$x++) {
				$sql .= "({$searchColumns[$x]} LIKE :t{$x})";
				if ($columnCount > 1 && $x != ($columnCount-1)) {
					$sql .= " OR ";
				}
				$bindparams[":t{$x}"] = "%".$term."%";
			}
			if ($result = $this->executeQuery($sql,$bindparams)) {
				return $result;
			}
		}
		return false;
	}

	protected function getSearchableColumns() {
		return $this->searchableColumns;
	}

	/**
	*	Get the row whose 'id' matches the passed $id
	*	
	*	@param mixed $id The unique identifier for the row
	*	@return array|false $results An array representing the resulting DB row, empty array if no match, false if the request failed
	*/
	public function getById($id) {
		$sql = "SELECT * FROM {$this->primaryTable} WHERE {$this->primaryKey}=:id";
		$temp = $this->executeQuery($sql,array(":id"=>$id));
		return $temp[0];
	}

	/**
	*	Remove a row from the DB with an 'id' matching the passed $id
	*	
	*	@param mixed $id The unique identifier for the row to be removed
	*	@return boolean $result true on removal, false on failure
	*/
	public function removeById($id) {
		$sql = "DELETE FROM {$this->primaryTable} WHERE {$this->primaryKey}=:id";
		return $this->executeUpdate($sql,array(":id"=>$id));
	}

	/**
	*	Insert a row into the DB using $data
	*	
	*	@param mixed[] $data The data to be inserted into the $primaryTable
	*	@return string|false The id of the inserted row on success, false on failure
	*/
	public function add($data) {
		return $this->buildInsertStatement($data);
	}

	/**
	*	Update the row having ID of $id with the key/value pairs in $data
	*	
	*	@param mixed $id The unique identifier for the row to be updated
	*	@param mixed[] $data The data with which to update the row
	*	@return boolean True on success, false on failure
	*/
	public function update($id,$data) {
		return $this->buildUpdateStatement($id,$data);
	}

	/**
	*	Get the Site context
	*	@return Interfaces\Site $site The Site context
	*/
	protected function getSite() {
		return $this->site;
	}

	/**
	*	Set the Site context
	*	@param Interfaces\Site $site The Site context
	*	@return void
	*/
	public function setSite($site) {
		$this->site = $site;
	}
}