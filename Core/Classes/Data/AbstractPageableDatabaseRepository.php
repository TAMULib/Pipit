<?php
namespace Core\Classes\Data;
use Core\Interfaces as Interfaces;
/**
*	A Pageable implementation of the DataBaseRepository interface
*	Extending this provides Pageable CRUD interaction with the configured database table
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

abstract class AbstractPageableDatabaseRepository extends AbstractDataBaseRepository implements Interfaces\PageableDataRepository {
	protected $resultsPerPage;

	/**
	*	Extending classes configure themselves using this constructor.
	*
	*	@param string $primaryTable Required. This specializes an instance of an extending class to the given DB table name
	*	@param string $primaryKey Required. Extending classes define the Primary Key of the table they manage
	*	@param string $defaultOrderBy Optional. Explicitly define a column to order query results by
	*	@param string[] $gettableColumns Optional. AbstractDataBaseRepository::get()) will SELECT only these fields, when passed
	*	@param string[] $searchableColumns Optional. AbstractDataBaseRepository::search()) will search these columns
    *   @param integer $resultsPerPage Optional. How many results to include per page. Defaults to 20
	*
	*/
	protected function __construct($primaryTable,$primaryKey,$defaultOrderBy=null,$gettableColumns=null,$searchableColumns=null,$resultsPerPage=20) {
		parent::__construct($primaryTable,$primaryKey,$defaultOrderBy,$gettableColumns,$searchableColumns);
		$this->setResultsPerPage($resultsPerPage);
	}

    /**
     * Returns the original query string plus sql LIMITing based on page data from a Core\Data\ResultsPage
     * @param string $query The sql query string
     * @param string The modified sql query string
     */
    protected function getPagedQuery($query,$resultsPage) {
        return $query." LIMIT ".(($resultsPage->getPage()-1)*$resultsPage->getResultsPerPage()).",{$resultsPage->getResultsPerPage()}";
    }

    /**
     * Returns a new Core\Data\ResultsPage
     * @param integer $page The page number
     * @param integer $resultsPerPage The number of results to include per page
     * @param string $query The sql query
     * @param integer $resultsCount The total number of results for the query
     * @param array $bindparams Optional. Any PDO parameters to bind with the query
     * @return Core\Data\ResultsPage
     */
    protected function getNewResultsPage($page,$resultsPerPage,$query,$resultsCount,$bindparams=null) {
        $resultsPage = ResultsPage::getNewResultsPage($page,$resultsPerPage);
        $resultsPage->setPageResults($this->executeQuery($this->getPagedQuery($query,$resultsPage),$bindparams),$resultsCount);
        return $resultsPage;
    }

    /**
     * Executes a count query using the base query inherited from Core\Data\AbstractDatabaseRepository
     * @return integer The total result count for the base query
     */
    protected function countGet() {
        $sql = "SELECT COUNT(*) {$this->getBaseQuery()}";
        return $this->executeQuery($sql)[0]['COUNT(*)'];
    }

    /**
     * Executes a count query using the base search query inherited from Core\Data\AbstractDatabaseRepository
     * @param string $term The search term
     * @return integer The total result count for the base search query
     */
    protected function countSearch($term) {
        if ($this->getSearchableColumns()) {
            $searchQuery = $this->getBaseSearchQuery($term);
            $searchQuery[0] = "SELECT COUNT(*) {$searchQuery[0]} ";

            if ($result = $this->executeQuery($searchQuery[0],$searchQuery[1])) {
                return $result[0]['COUNT(*)'];
            }
        }
        return false;
    }

    /**
    *	Set the number of results per page for the Repository
    *	@param integer $resultsPerPage
    *	@return void
    */
    protected function setResultsPerPage($resultsPerPage) {
        $this->resultsPerPage = $resultsPerPage;
    }

	/**
	*	Get the results of the base query for the given page number
	*   @param integer $page Optional. The page of results to retrieve. Defaults to 1
	*	@return Core\Data\ResultsPage The ResultsPage of the base query for the given page
	*/
    public function pagedGet($page=1) {
        return $this->getNewResultsPage($page,$this->resultsPerPage,$this->getGetQuery(),$this->countGet());
    }

    /**
	*	Get the results of the base search query for the given search term and page number
    *   @param string $term The search term
	*   @param integer $page Optional. The page of results to retrieve. Defaults to 1
	*	@return Core\Data\ResultsPage The ResultsPage of the base search query for the given term and page
	*/
    public function pagedSearch($term,$page=1) {
        $searchQuery = $this->getSearchQuery($term);
        return $this->getNewResultsPage($page,$this->resultsPerPage,$searchQuery[0],$this->countSearch($term),$searchQuery[1]);
    }
}
