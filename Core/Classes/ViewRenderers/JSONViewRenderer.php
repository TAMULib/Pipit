<?php
namespace Core\Classes\ViewRenderers;
use Core\Interfaces as Interfaces;

/** 
*	An implementation of the ViewRenderer interface for rendering registered viewvariables as JSON
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class JSONViewRenderer implements Interfaces\ViewRenderer {
	/** @var mixed[] $variables An array of application data to provide to the views */
	private $variables = array();
	/** @var mixed[] $appContext An array of data for the views geared toward the app environment (User session, server paths, config)  */
	private $appContext = null;

	/**
	*	Render as JSON any registered view variables
	*/
	public function renderView() {
		echo json_encode($this->getViewVariables());
	}

	/*
	*	An implmentation is required by the ViewRenderer interface, but this method is not currently used by JSONViewRenderer
	*/
	public function setView($viewFile,$isAdmin=false) {
	}

	public function setViewVariables($data) {
		$this->variables = $data;
	}

	public function registerViewVariable($name,$data) {
		$this->variables[$name] = $data;
	}

	public function getViewVariables() {
		return $this->variables;
	}

	public function getViewVariable($name) {
		return $this->variables[$name];
	}

	public function registerAppContextProperty($name,$data) {
		$this->appContext[$name] =& $data;
	}

	public function getAppContextProperty($name) {
		return $this->appContext[$name];
	}

	/*
	*	An implmentation is required by the ViewRenderer interface, but this method is not currently used by JSONViewRenderer
	*/
	public function setPage($page) {
	}
}
?>