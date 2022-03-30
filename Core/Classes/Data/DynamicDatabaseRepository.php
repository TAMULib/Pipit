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
	* @param \Core\Classes\Configuration\DynamicDatabaseRepositoryConfiguration $configuration An instance of DynamicRepositoryConfiguration
	*/
	public function __construct($configuration) {
		$config = $configuration;
		parent::__construct($config->getTableName(),$config->getPrimaryKey(),$config->getDefaultOrderBy(),$config->getGettableColumns(),$config->getSearchableColumns());
	}
}