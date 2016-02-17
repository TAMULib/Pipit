<?php
class htmlviewrenderer implements ViewRenderer {
	private $variables = array();
	private $viewFile = null;

	public function __construct(&$config,&$globaluser,&$pages) {
		$this->registerViewVariable("config",$config);
		$this->registerViewVariable("globaluser",$globaluser);
		$this->registerViewVariable("pages",$pages);
	}

	public function renderView() {
		$config = $this->getViewVariable("config");
		$globaluser = $this->getViewVariable("globaluser");
		$pages = $this->getViewVariable("pages");
		include "{$config['path_app']}layouts/header.lo.php";
		if ($this->viewFile) {
			$parameters = $this->getViewVariables();
			include $this->viewFile;
		} else {
			echo 'Error loading view';
		}
		include "{$config['path_app']}layouts/footer.lo.php";
	}

	public function setView($viewFile,$isAdmin=false) {
		$config = $this->getViewVariable("config");
		$fullPath = "{$config['path_views']}".(($isAdmin) ? 'admin/':'')."{$viewFile}";
		if (is_file($fullPath)) {
			$this->viewFile = $fullPath;
		}
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
}
?>