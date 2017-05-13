<?php
namespace Core\Classes\Data;
/** 
*	A basic DB Repository class providing generic CRUD interaction with the configured database table
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class SimpleDataBaseRepository extends AbstractDatabaseRepository {
	public function __construct($tableName,$primaryKey,$defaultOrderBy=null,$gettableColumns=null,$searchableColumns=null) {
		parent::__construct($tableName,$primaryKey,$defaultOrderBy,$gettableColumns,$searchableColumns);
	}
}