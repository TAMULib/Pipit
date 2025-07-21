<?php
namespace Pipit\Classes\Data;
use Pipit\Classes\Configuration\DynamicDataBaseRepositoryConfiguration;
/** 
*	A basic DB Repository class providing generic CRUD interaction with the configured database table
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class DynamicDataBaseRepository extends AbstractDataBaseRepository {
    /**
    * Constructor for DynamicDataBaseRepository
    *
    * @param \Pipit\Classes\Configuration\DynamicDataBaseRepositoryConfiguration $configuration An instance of DynamicDataBaseRepositoryConfiguration
    */
    public function __construct(DynamicDataBaseRepositoryConfiguration $configuration) {
        parent::__construct($configuration->getTableName(),$configuration->getPrimaryKey(),$configuration->getDefaultOrderBy(),$configuration->getGettableColumns(),$configuration->getSearchableColumns());
    }
}