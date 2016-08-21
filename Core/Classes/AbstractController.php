<?php
namespace Core\Classes;
use Core\Interfaces as Interfaces;
/** 
*	An abstract implementation of the Controller interface, intended to be extended by App controllers
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

abstract class AbstractController extends CoreObject implements Interfaces\Controller {
	/** @var Interfaces\Site This must be injected on construction */
	protected $site;
	/** @var Interfaces\SitePage The specific SitePage, if one exists, that represents the Controller in the UI */
	protected $page;
	/** @var string An identifier for the view an endpoint method wants the ViewRenderer to load */
	protected $viewName;
	/** @var boolean A flag identifying a Controller for admin access only */
	protected $requireAdmin = false;
	/** @var mixed[] Optional Configuration directives for a Controller */
	protected $controllerConfig = array();

	/**
	*	@param Interfaces\Site $site - the context for the page load
	*	@param mixed[] $controllerConfig - An (optional) associative array of Controller specific configuration directives
	*	@return void
	*/
	public function __construct(&$site,$controllerConfig=null) {
		$this->site = $site;
		$this->setPage($site->getCurrentPage());
		if ($controllerConfig) {
			$this->setControllerConfig($controllerConfig);
		}
		$this->configure();
	}

	/**
	*	Assigns a SitePage to the controller
	*	@param Interfaces\SitePage $page
	*	@return void
	*/
	protected function setPage($page) {
		$this->page = $page;
	}

	/**
	*	Returns the assigned SitePage
	*	@return Interfaces\SitePage
	*/
	protected function getPage() {
		return $this->page;
	}

	/**
	*	Controller endpoint methods should use this to set the name of the view for their Controller
	*	@param string $viewName
	*	@return void
	*	@see AbstractController::evaluate() How the $viewName property is used to feed the Interfaces\ViewRenderer
	*/
	protected function setViewName($viewName) {
		$this->viewName = $viewName;
	}

	/**
	*	Returns the assigned viewName
	*	@return string $viewName
	*/
	protected function getViewName() {
		return $this->viewName;
	}

	/**
	*	Set the $controllerConfig property, which can be used to provide configuration directives to extending Controllers
	*	@param mixed[] $controllerConfig
	*	@return void
	*/
	protected function setControllerConfig($controllerConfig) {
		$this->controllerConfig = $controllerConfig;
	}

	/**
	*	Returns the assigned $controllerConfig
	*	@return mixed[] $controllerConfig
	*/
	protected function getControllerConfig() {
		return $this->controllerConfig;
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

	/**
	*	Override to handle any Controller specific configurations, e.g. Page details, DataRepository fetching.
	*/
	protected function configure() {
	}

	/**
	*	This is a extending Controller's default endpoint method, executed by ::evaluate(), when no other endpoint methods match the request
	*/
	abstract protected function loadDefault();
}