<?php
namespace Core\Classes;
use Core\Interfaces as Interfaces;
/** 
*	An abstract implementation of the Controller interface, intended to be extended by App controllers
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

abstract class AbstractController extends CoreObject implements Interfaces\Controller {
	protected $site;
	protected $page;
	protected $viewName;
	protected $requireAdmin = false;
	protected $controllerConfig = array();

	/**
	*	@param Interfaces\Site $site - the context for the page load
	*	@param mixed[] $controllerConfig - An array of Controller specific configuration directives
	*
	*/
	public function __construct(&$site,$controllerConfig=null) {
		$this->site = $site;
		$this->setPage($site->getCurrentPage());
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

	/**
	*	This evaluate() implementation looks for $action and $subaction vars from the request input and uses them to build a method name. If that method name exists, it will be invoked. If not, loadDefault() will be called. Any ViewRenderer updates made as a result are then registered with the Site's ViewRenderer.
	*/
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