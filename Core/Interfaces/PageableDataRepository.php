<?php
namespace Core\Interfaces;
/**
*	An interface defining a DataRepository with Pageable result sets
*	DataRepositories are utilized to perform CRUD actions on data stores
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface PageableDataRepository extends DataRepository {
    /**
    *	Get a page of records from a data source
    *
    *	@return ResultsPage The results
    */
    public function pagedGet($page);

    /**
    * 	Get a page of search results
    *
    *	@param mixed $data The search criteria
    *   @param integer $page The page number to return
    *	@return ResultsPage The search results
    *
    */
    public function pagedSearch($data,$page);

}
?>
