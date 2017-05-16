<?php
namespace Core\Classes\Data;
/** 
*	A basic DB Repository class providing generic CRUD interaction with the configured database table
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class SimpleDataBaseRepository extends AbstractDatabaseRepository {
	/**
	* Constructor for SimpleDatabaseRepository
	*
	* @param SimpleRepositoryConfiguration $configuration An instance of SimpleRepositoryConfiguration
	*/
	public function __construct($configuration) {
		parent::__construct($configuration->getTableName(),$configuration->getPrimaryKey(),$configuration->getDefaultOrderBy(),$configuration->getGettableColumns(),$configuration->getSearchableColumns());
	}
}