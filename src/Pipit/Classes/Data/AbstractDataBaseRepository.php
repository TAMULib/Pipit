<?php
namespace Pipit\Classes\Data;
use Pipit\Interfaces as Interfaces;
/**
*	An abstract implementation of the DataBaseRepository interface
*	Extending this provides CRUD interaction with the configured database table
*
*	@todo Provide another layer to allow for non MySQL flavors of SQL
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

abstract class AbstractDataBaseRepository extends DBObject implements Interfaces\DataRepository, Interfaces\Configurable {
	/** @var Interfaces\Site $site This provides the Site context to all DatabaseRepositories extending this class */
	protected $site;
	/** @var string $primaryTable This is the name of the DB table managed by DatabaseRepositories extending this class */
	protected $primaryTable;
	/** @var string $primaryKey This is the name of the Primary Key for the $primaryTable managed by DatabaseRepositories extending this class */
	protected $primaryKey = 'id';
	/** @var string|null $defaultOrderBy If provided, AbstractDataBaseRepository::get()) will ORDER BY this property. */
	protected $defaultOrderBy;
	/** @var string[]|null $gettableColumns If provided, AbstractDataBaseRepository::get()) will only SELECT the columns present in this array. */
	protected $gettableColumns;
	/** @var string[]|null $searchableColumns If provided, AbstractDataBaseRepository::search()) will carry out its search on these columns */
	protected $searchableColumns;

	/**
	*	Extending classes configure themselves using this constructor.
	*
	*	@param string $primaryTable Required. This specializes an instance of an extending class to the given DB table name
	*	@param string $primaryKey Required. Extending classes define the Primary Key of the table they manage
	*	@param string|null $defaultOrderBy Optional. Explicitly define a column to order query results by
	*	@param string[]|null $gettableColumns Optional. AbstractDataBaseRepository::get()) will SELECT only these fields, when passed
	*	@param string[]|null $searchableColumns Optional. AbstractDataBaseRepository::search()) will search these columns
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
	*	@return mixed[]|false $results A two dimensional array representing the resulting rows: array(array("id"=>1,"field"=>"value1"),array("id"=>2","field"=>"value2")), false on failure
	*/
	public function get() {
		return $this->queryWithIndex($this->getGetQuery(),$this->primaryKey);
	}

	/**
	*	Get all rows from the $primaryTable matching the search %$term% against a 'name' field
	*
	*	@param string $term The search criteria
	*	@return mixed[]|false $results A two dimensional array representing the resulting rows: array(array("id"=>1,"field"=>"value1"),array("id"=>2","field"=>"value2")), false on failure
	*/
	public function search($term) {
		if ($this->getSearchableColumns()) {
			$searchQuery = $this->getSearchQuery($term);
			if ($result = $this->executeQuery($searchQuery['sql'],$searchQuery['bindparams'])) {
				return $this->processResults($result);
			}
		}
		return false;
	}

	/**
	*	Get all rows from the $primaryTable matching the provided field/value pairs search %$term% against a 'name' field
	*
	*	@param array<string,string> $data The search criteria field/value pair(s)
	*	@return mixed[]|false $results A two dimensional array representing the resulting rows: array(array("id"=>1,"field"=>"value1"),array("id"=>2","field"=>"value2")), false on failure
	*/
	public function searchAdvanced($data) {
		$sql = "SELECT * FROM {$this->primaryTable} u ";
		$conj = "WHERE";
		$bindparams = array();
		foreach ($data as $field=>$value) {
			$sql .= "{$conj} {$field}=:{$field} ";
			$bindparams[":{$field}"] = $value;
			$conj = "AND";
		}

		if ($result = $this->executeQuery($sql,$bindparams)) {
			return $this->processResults($result);
		}
		return false;
	}

	/**
	* Returns an array of column names that should be used to perform searches on records in a repository
	* @return string[]|null $searchableColumns An array of string containing the searchable columns
	*/
	protected function getSearchableColumns() {
		return $this->searchableColumns;
	}

	/**
	*	Get the row whose 'id' matches the passed $id
	*
	*	@param mixed $id The unique identifier for the row
	*	@return mixed|false $results An array representing the resulting DB row, empty array if no match, false if the request failed
	*/
	public function getById($id) {
		$sql = "SELECT * FROM {$this->primaryTable} WHERE {$this->primaryKey}=:id";
		if ($temp = $this->executeQuery($sql,array(":id"=>$id))) {
			return $this->processResult($temp);
		}
		return false;
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
	*	@param string $id The unique identifier for the row to be updated
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

	public function configure(Interfaces\Site $site) {
		$this->setSite($site);
	}

	/**
	 * Provides the default get query for the extending repository
	 * @return string
	 */
	protected function getGetQuery() {
		$sql = "SELECT ".(($this->gettableColumns) ? "{$this->primaryKey},".implode(",",$this->gettableColumns):"*")."  {$this->getBaseQuery()}";
		if ($this->defaultOrderBy) {
			$sql .= " ORDER BY {$this->defaultOrderBy}";
		}
		return $sql;
	}

	/**
	 * Provides the base query for the extending repository
	 * @return string
	 */
	protected function getBaseQuery() {
		return "FROM {$this->primaryTable}";
	}

	/**
	 * Provides the default search query for the extending repository
	 * @param string $term The search term
	 * @return array{'sql': string, 'bindparams': array<string,string>}
	 */
	protected function getSearchQuery($term) {
		$searchQuery = $this->getBaseSearchQuery($term);
		$searchQuery['sql'] = "SELECT * {$searchQuery['sql']} ";
		return $searchQuery;
	}

	/**
	 * Provides the default search query base for the extending repository
	 * @param string $term The search term
	 * @return array{'sql': string, 'bindparams': array<string,string>}
	 */
	protected function getBaseSearchQuery($term) {
		$sql = "FROM {$this->primaryTable} WHERE ";
		$searchColumns = $this->getSearchableColumns();
		$bindparams = array();
		if (is_array($searchColumns)) {
			$columnCount = count($searchColumns);
			for ($x=0;$x<$columnCount;$x++) {
				$sql .= "({$searchColumns[$x]} LIKE :t{$x})";
				if ($columnCount > 1 && $x != ($columnCount-1)) {
					$sql .= " OR ";
				}
				$bindparams[":t{$x}"] = "%".$term."%";
			}
		}
		$queryParts = [];
		$queryParts['sql'] = $sql;
		$queryParts['bindparams'] = $bindparams;
		return $queryParts;
	}

	/**
	 * Apply any needed processing to a single db result array
	 * @param mixed[] $result
	 * @return mixed[]|\Pipit\Interfaces\Entity
	 */
	protected function processResult($result) {
		$resultRow = current($result);
		if ($this instanceof Interfaces\EntityRepository && is_callable($this->getEntityBuilder())) {
			$resultRow = $this->getEntityBuilder()($resultRow);
			if ($resultRow instanceof Interfaces\Entity) {
				return $resultRow;
			}
		}
		return $resultRow;
	}

	/**
	 * Apply any needed processing to a db results array
	 * @param mixed[] $results
	 * @return mixed[]
	 */
	protected function processResults($results) {
        if (!is_array($results)) {
            $results = [];
        } else if ($this instanceof Interfaces\EntityRepository && is_callable($this->getEntityBuilder())) {
            $results = array_map($this->getEntityBuilder(),$results);
        }
		return $results;
	}
}
