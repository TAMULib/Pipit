<?php
namespace TAMU\Seed\Classes\ViewRenderers;
use TAMU\Seed\Interfaces as Interfaces;

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