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
	protected $controllerConfig = array();

	public function __construct(&$site,$controllerConfig=null) {
		$this->site = $site;
		if ($controllerConfig) {
			$this->setControllerConfig($controllerConfig);
		}
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

	protected function getControllerConfig() {
		return $this->controllerConfig;
	}

	protected function setControllerConfig($controllerConfig) {
		$this->controllerConfig = $controllerConfig;
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