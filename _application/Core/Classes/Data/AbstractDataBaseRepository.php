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

abstract class AbstractDataBaseRepository extends DBObject implements Interfaces\DataBaseRepository {
	protected $primaryTable;
	protected $primaryKey;
	protected $defaultOrderBy;
	protected $gettableColumns;

	protected function __construct($primaryTable,$primaryKey,$defaultOrderBy=null,$gettableColumns=null) {
		$this->primaryTable = $primaryTable;
		$this->primaryKey = $primaryKey;
		$this->defaultOrderBy = $defaultOrderBy;
		$this->gettableColumns = $gettableColumns;
		parent::__construct();
	}

	public function get() {
		$sql = "SELECT ".(($this->gettableColumns) ? "id,".implode(",",$this->gettableColumns):"*")." FROM {$this->primaryTable}";
		if ($this->defaultOrderBy) {
			$sql .= " ORDER BY {$this->defaultOrderBy}";
		}
		return $this->queryWithIndex($sql,$this->primaryKey);
	}

	public function search($term) {
		$sql = "SELECT * FROM {$this->primaryTable} WHERE 
				name LIKE ?";
		$bindparams = array("%".$term."%");
		if ($result = $this->executeQuery($sql,$bindparams)) {
			return $result;
		}
		return false;
	}

	public function getById($id) {
		$sql = "SELECT * FROM {$this->primaryTable} WHERE id=:id";
		$temp = $this->executeQuery($sql,array(":id"=>$id));
		return $temp[0];
	}

	public function removeById($id) {
		$sql = "DELETE FROM {$this->primaryTable} WHERE {$this->primaryKey}=:id";
		return $this->executeUpdate($sql,array(":id"=>$id));
	}

	public function add($data) {
		return $this->buildInsertStatement($data);
	}

	public function update($id,$data) {
		return $this->buildUpdateStatement($id,$data);
	}
}