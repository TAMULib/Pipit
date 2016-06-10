<?php
namespace Core\Classes\ViewRenderers;
use Core\Interfaces as Interfaces;

/** 
*	An implementation of the ViewRenderer interface for rendering registered viewvariables as JSON
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class JSONViewRenderer implements Interfaces\ViewRenderer {
	private $variables = array();
	private $appContext = null;

	public function renderView() {
		echo json_encode($this->getViewVariables());
	}

	//ViewRenderer interface required, but not currently used by json renderer
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

	//ViewRenderer interface required, but not currently used by json renderer
	public function setPage($page) {
	}
}
?>