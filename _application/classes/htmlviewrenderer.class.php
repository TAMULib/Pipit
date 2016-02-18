<?php
class htmlviewrenderer implements ViewRenderer {
	private $variables = array();
	private $viewFile = null;
	private $appContext = null;

	public function __construct() {
		$this->registerAppContextProperty("config", $GLOBALS['config']);
		$this->registerAppContextProperty("globaluser", $GLOBALS['globaluser']);
		$this->registerAppContextProperty("pages", $GLOBALS['pages']);
		$this->registerAppContextProperty("system", $GLOBALS['system']);
	}

	public function renderView() {
		$config =& $this->getAppContextProperty("config");
		$globaluser =& $this->getAppContextProperty("globaluser");
		$pages =& $this->getAppContextProperty("pages");
		$system =& $this->getAppContextProperty("system");
		$page =& $this->getAppContextProperty("page");
		$app_http =& $this->getAppContextProperty("app_http");
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
		$config = $this->getAppContextProperty("config");
		$fullPath = "{$config['path_views']}".(($isAdmin) ? 'admin/':'')."{$viewFile}.view.php";
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

	public function registerAppContextProperty($name,$data) {
		$this->appContext[$name] =& $data;
	}

	public function getAppContextProperty($name) {
		return $this->appContext[$name];
	}

	public function setPage($page) {
		$this->registerAppContextProperty("page",$page);
	}
}
?>