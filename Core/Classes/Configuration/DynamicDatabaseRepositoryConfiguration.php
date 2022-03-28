<?php
namespace Core\Classes\Configuration;
/** 
*	A Configuration class representing a DynamicDatabaseRepository configuration
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class DynamicDatabaseRepositoryConfiguration extends AbstractConfiguration {
	/** @var string $tableName The name of the base table */
	protected $tableName;
	/** @var string $primaryKey The name of the primary key for the base table */
	protected $primaryKey;
	/** @var string $defaultOrderBy The column name to sort by for default queries */
	protected $defaultOrderBy;
	/** @var string[] $gettableColumns The column names to include in default queries */
	protected $gettableColumns;
	/** @var string[] $searchableColumns The column names to apply search terms against in search queries */
	protected $searchableColumns;

	/**
	 * @param string $tableName The name of the base table
	 * @param string $primaryKey The name of the primary key for the base table
	 * @param string $defaultOrderBy The column name to sort by for default queries
	 * @param string[] $gettableColumns The column names to include in default queries
	 * @param string[] $searchableColumns The column names to apply search terms against in search queries
	 *
	 */
	public function __construct($tableName,$primaryKey,$defaultOrderBy=null,$gettableColumns=null,$searchableColumns=null) {
		$this->tableName = $tableName;
		$this->primaryKey = $primaryKey;
		$this->defaultOrderBy = $defaultOrderBy;
		$this->gettableColumns = $gettableColumns;
		$this->searchableColumns = $searchableColumns;
	}

	/**
	 * Provides the name of the base table
	 * @return string $tableName The name of the base table
	 */
	public function getTableName() {
		return $this->tableName;
	}

	/**
	 * Provides the name of the primary key column
	 * @return string $primaryKey The name of the primary key column
	 */
	public function getPrimaryKey() {
		return $this->primaryKey;
	}

	/**
	 * Provides the default column(s) to order queries by
	 * @return string
	 */
	public function getDefaultOrderBy() {
		return $this->defaultOrderBy;
	}

	/**
	 * Provides the columns to SELECT in queries
	 * @return string[]
	 */
	public function getGettableColumns() {
		return $this->gettableColumns;
	}

	/**
	 * Provides the columns to check search terms against in search queries
	 * @return string[]
	 */
	public function getSearchableColumns() {
		return $this->searchableColumns;
	}
}
