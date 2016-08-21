<?php
namespace Core\Classes\ViewRenderers;
use Core\Interfaces as Interfaces;

/** 
*	The default implementation of the ViewRenderer interface.
*	Renders HTML with templated header and footer
*	Would make a good starting point for integration with front end frameworks like Bootstrap
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/


class HTMLViewRenderer implements Interfaces\ViewRenderer {
	private $variables = array();
	private $viewFile = null;
	private $appContext = null;
	private $viewPath = '';
	private $adminPath = '';

	public function __construct($globalUser,$pages,$data,$controllerName) {
		$this->registerAppContextProperty("config", $GLOBALS['config']);
		$this->registerAppContextProperty("globalUser", $globalUser);
		$this->registerAppContextProperty("pages", $pages);
		$this->registerAppContextProperty("data", $data);
		$this->registerAppContextProperty("controllerName", $controllerName);
		$this->setViewPath('html');
		$this->setAdminViewPath('admin');
	}

	public function renderView() {
		$config = $this->getAppContextProperty("config");
		$globalUser = $this->getAppContextProperty("globalUser");
		$pages = $this->getAppContextProperty("pages");
		$page = $this->getAppContextProperty("page");
		$data = $this->getAppContextProperty("data");
		$app_http = $this->getAppContextProperty("app_http");
		$controllerName = $this->getAppContextProperty("controllerName");
		$systemMessages = $this->getAppContextProperty("systemMessages");
		
		include "{$this->getViewPath()}layouts/header.lo.php";
		if (!empty($this->getViewFile())) {
			$parameters = $this->getViewVariables();
			include $this->getViewFile();
		}
		include "{$this->getViewPath()}layouts/footer.lo.php";
	}

	public function setView($viewFile,$isAdmin=false) {
		$config = $this->getAppContextProperty("config");
		$fullPath = (($isAdmin) ? $this->getAdminViewPath():$this->getViewPath())."{$viewFile}.view.php";
		if (is_file($fullPath)) {
			$this->viewFile = $fullPath;
			return true;
		}
		return false;
	}

	protected function getViewFile() {
		return $this->viewFile;
	}

	protected function getViewPath() {
		return "{$this->getAppContextProperty('config')['PATH_VIEWS']}{$this->viewPath}/";
	}

	protected function setViewPath($viewPath) {
		$this->viewPath = $viewPath;
	}

	protected function getAdminViewPath() {
		return "{$this->getAppContextProperty('config')['PATH_VIEWS']}{$this->viewPath}/{$this->adminPath}/";
	}

	protected function setAdminViewPath($adminPath) {
		$this->adminPath = $adminPath;
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
		if (isset($this->appContext[$name])) {
			return $this->appContext[$name];
		}
		return null;
	}

	public function setPage($page) {
		$this->registerAppContextProperty("page",$page);
	}
}
?>