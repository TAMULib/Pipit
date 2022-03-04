<?php
namespace Core\Classes\Data;
use Core\Interfaces as Interfaces;
/**
*	A Pageable implementation of the DataBaseRepository interface
*	Extending this provides Pageable CRUD interaction with the configured database table
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class PageableDatabaseRepository extends AbstractDataBaseRepository implements Interfaces\PageableDataRepository {

    protected function getPagedQuery($query,$resultsPage) {
        return $query." LIMIT ".(($resultsPage->getPage()-1)*$resultsPage->getResultsPerPage()).",{$resultsPage->getResultsPerPage()}";
    }

    protected function getNewResultsPage($page,$resultsPerPage,$query,$resultsCount,$bindparams=null) {
        $resultsPage = new ResultsPage();
        $resultsPage->setPage($page);
        if ($resultsPerPage) {
            $resultsPage->setResultsPerPage($resultsPerPage);
        }

        $resultsPage->calculatePageCount($resultsCount);
        $resultsPage->setPageResults($this->executeQuery($this->getPagedQuery($this->getGetQuery(),$resultsPage)));
        return $resultsPage;
    }

    public function pagedGet($page=1,$resultsPerPage=null) {
        return $this->getNewResultsPage($page,$resultsPerPage,$this->getGetQuery(),$this->countGet());
    }

    public function pagedSearch($term,$page=1,$resultsPerPage=null) {
        $searchQuery = $this->getSearchQuery($term);
        return $this->getNewResultsPage($page,null,$searchQuery[0],$this->countSearch($term),$searchQuery[1]);
    }

    public function countGet() {
        $sql = "SELECT COUNT(*) {$this->getBaseQuery()}";
        return $this->executeQuery($sql)[0]['COUNT(*)'];
    }

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


}

?>
