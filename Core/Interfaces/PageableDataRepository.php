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
	*	@return mixed[] The results
	*/
	public function pagedGet($page);

	/**
	* 	Get a page of search results
	*
	*	@param mixed $data The search criteria
	*	@return mixed[] The search results
	*	
	*/
	public function pagedSearch($page,$data);

}
?>