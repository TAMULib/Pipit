<?php
namespace Pipit\Classes\Data;
use Pipit\Interfaces as Interfaces;
/**
 * Represents a Page of DataRepository results
 *
 * @author Jason Savell <jsavell@library.tamu.edu>
 */
class ResultsPage implements \JsonSerializable {
    /** @var int $page The page number represented by this ResultsPage */
    private $page = 1;
    /** @var int $resultsPerPage The number of results included per page */
    private $resultsPerPage = 25;
    /** @var int $pageCount The total number of pages in this result */
    private $pageCount;
    /** @var mixed[] A two dimensional array representing the resulting rows: array(array("id"=>1,"field"=>"value1"),array("id"=>2","field"=>"value2")) */
    private $pageResults;

    protected function __construct() {

    }

    /**
    *	Set the page number
    *	@param integer $page The page number
    *	@return void
    */
    public function setPage($page) {
        $this->page = $page;
    }

    /**
    *	Set the number of results per page
    *	@param integer $resultsPerPage
    *	@return void
    */
    public function setResultsPerPage($resultsPerPage) {
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
    *	Calculate and set the page count based on a results count
    *	@param integer $resultsCount
    *	@return void
    */
    protected function calculatePageCount($resultsCount) {
        $this->setPageCount(intval(ceil($resultsCount/$this->getResultsPerPage())));
    }

    /**
    *	Set the page count
    *	@param integer $pageCount
    *	@return void
    */
    private function setPageCount($pageCount) {
        $this->pageCount = $pageCount;
    }

    /**
    *	Set the page results
    *	@param mixed[] $results
    *	@param integer $resultsCount
    *	@return void
    */
    public function setPageResults($results,$resultsCount) {
        $this->calculatePageCount($resultsCount);
        $this->pageResults = $results;
    }

    /**
    *	Get the page number of this ResultsPage
    *
    *	@return integer The page number
    */
    public function getPage() {
        return $this->page;
    }

    /**
    *	Get the results
    *
    *	@return mixed[] A two dimensional array representing the resulting rows: array(array("id"=>1,"field"=>"value1"),array("id"=>2","field"=>"value2"))
    */
    public function getPageResults() {
        return $this->pageResults;
    }

    /**
    *	Get the results per page for this ResultsPage
    *
    *	@return integer The page number
    */
    public function getResultsPerPage() {
        return $this->resultsPerPage;
    }

    /**
    *	Get the total number of pages for this results set
    *
    *	@return integer The total number of pages
    */
    public function getPageCount() {
        return $this->pageCount;
    }

    /**
    *	Creates a new ResultsPage
    *   @param int $page The desired page number of the results
    *   @param int $resultsPerPage The number of results to include per page
    *	@return \Pipit\Classes\Data\ResultsPage A new ResultsPage, ready to be populated with results
    */
    public static function getNewResultsPage(int $page, int $resultsPerPage) {
        $resultsPage = new ResultsPage();
        $resultsPage->setPage(intval($page));
        $resultsPage->setResultsPerPage($resultsPerPage);
        return $resultsPage;
    }

    public function jsonSerialize():mixed {
        return get_object_vars($this);
    }
}
