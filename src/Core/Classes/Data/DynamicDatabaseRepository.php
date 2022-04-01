<?php
namespace Core\Classes\Data;
use Core\Classes\Configuration\DynamicDatabaseRepositoryConfiguration;
/** 
*	A basic DB Repository class providing generic CRUD interaction with the configured database table
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class DynamicDataBaseRepository extends AbstractDataBaseRepository {
	/**
	* Constructor for DynamicDatabaseRepository
	*
	* @param \Core\Classes\Configuration\DynamicDatabaseRepositoryConfiguration $configuration An instance of DynamicDatabaseRepositoryConfiguration
	*/
	public function __construct(DynamicDatabaseRepositoryConfiguration $configuration) {
		parent::__construct($configuration->getTableName(),$configuration->getPrimaryKey(),$configuration->getDefaultOrderBy(),$configuration->getGettableColumns(),$configuration->getSearchableColumns());
	}
}