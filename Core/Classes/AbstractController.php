<?php
namespace Core\Classes;
use Core\Interfaces as Interfaces;
/** 
*	An abstract implementation of the Controller interface
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

abstract class AbstractController extends CoreObject implements Interfaces\Controller {
	protected $site;
	protected $page = array();
	protected $viewName;
	protected $requireAdmin = false;

	public function __construct(&$site) {
		$this->site = $site;
	}

	protected function setPage($page) {
		$this->page = $page;
	}

	protected function getPage() {
		return $this->page;
	}

	protected function setViewName($viewName) {
		$this->viewName = $viewName;
	}

	protected function getViewName() {
		return $this->viewName;
	}

	public function evaluate() {
		$data = $this->site->getSanitizedInputData();
		if (!empty($data['action'])) {
			$methodName = $data['action'];
			if (!empty($data['subaction'])) {
				$methodName = $methodName.$data['subaction'];
			}
			if (method_exists($this,$methodName)) {
				$this->$methodName();
			}
		} else {
			$this->loadDefault();
		}
		$this->site->getViewRenderer()->setPage($this->getPage());
		if (!empty($this->getViewName())) {
			$this->site->getViewRenderer()->setView($this->getViewName(),$this->requireAdmin);
		}
	}

	abstract protected function loadDefault();
}