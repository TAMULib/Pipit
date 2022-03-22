<?php
namespace Core\Classes\Data;
/** 
*	A basic DB Repository class providing generic CRUD interaction with the configured database table
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class DynamicDataBaseRepository extends AbstractDataBaseRepository {
	/**
	* Constructor for DynamicDatabaseRepository
	*
	* @param DynamicRepositoryConfiguration $configuration An instance of DynamicRepositoryConfiguration
	*/
	public function __construct($configuration) {
		parent::__construct($configuration->getTableName(),$configuration->getPrimaryKey(),$configuration->getDefaultOrderBy(),$configuration->getGettableColumns(),$configuration->getSearchableColumns());
	}
}