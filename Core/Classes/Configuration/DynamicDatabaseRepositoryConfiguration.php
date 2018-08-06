<?php
namespace Core\Classes\Configuration;

/**
*   A Configuration class representing a DynamicDatabaseRepository configuration
*   @author Jason Savell <jsavell@library.tamu.edu>
*/
class DynamicDatabaseRepositoryConfiguration extends AbstractConfiguration {
    protected $tableName;
    protected $primaryKey;
    protected $defaultOrderBy;
    protected $gettableColumns;
    protected $searchableColumns;

    public function __construct($tableName,$primaryKey,$defaultOrderBy=null,$gettableColumns=null,$searchableColumns=null) {
        $this->tableName = $tableName;
        $this->primaryKey = $primaryKey;
        $this->defaultOrderBy = $defaultOrderBy;
        $this->gettableColumns = $gettableColumns;
        $this->searchableColumns = $searchableColumns;
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function getPrimaryKey() {
        return $this->primaryKey;
    }

    public function getDefaultOrderBy() {
        return $this->defaultOrderBy;
    }

    public function getGettableColumns() {
        return $this->gettableColumns;
    }

    public function getSearchableColumns() {
        return $this->searchableColumns;
    }
}
?>
