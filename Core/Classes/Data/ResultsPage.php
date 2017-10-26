<?php
namespace Core\Classes\Data;
use Core\Interfaces as Interfaces;
/** 
 * Represents a Page of DataRepository results
 *
 * @author Jason Savell <jsavell@library.tamu.edu>
 */
class ResultsPage {
	private $page;
	private $resultsPerPage = 25;
	private $pageCount;
	private $pageResults;

	public function setPage($page) {
		$this->page = $page;
	}

	public function setResultsPerPage($rpp) {
		$this->resultsPerPage = $rpp;
	}

	public function calculatePageCount($resultsCount) {
		$this->setPageCount(ceil($resultsCount/$this->getResultsPerPage()));
	}

	protected function setPageCount($pageCount) {
		$this->pageCount = $pageCount;
	}

	public function setPageResults($results) {
		$this->pageResults = $results;
	}
	
	public function getPage() {
		return $this->page;
	}

	public function getPageResults() {
		return $this->pageResults;
	}

	public function getResultsPerPage() {
		return $this->resultsPerPage;
	}
}