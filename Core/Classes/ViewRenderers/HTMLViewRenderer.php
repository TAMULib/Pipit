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
	/** @var mixed[] $variables An array of application data to provide to the views */
	private $variables = array();
	/** @var string $viewFile The filename of the view to load */
	private $viewFile = null;
	/** @var mixed[] $appContext An array of data for the views geared toward the app environment (User session, server paths, config)  */
	private $appContext = null;
	/** @var string $viewPath The directory path to the views */
	private $viewPath = '';
	/** @var string $adminPath The directory within the $viewPath that contains the admin views */
	private $adminPath = '';
	/** @var string $viewDirectory An optional subdirectory within the $viewPath that contains a collection of related views */
	private $viewDirectory = '';
	

	/**
	*	Initializes the ViewRenderer with the state of the application
	*	@param Core\Data\User $globalUser The User or UserCAS
	*	@param Core\Interfaces\SitePage[] $pages The application pages
	*	@param mixed[] $data The data to be presented by the views
	*	@param string $controllerName The name of the active Controller. Helps with discrete loading of Controller specific static resources
	*	@todo Either handle the case of the missing $GLOBALS['config'] with a meaningful exception or ideally, 
	*/
	public function __construct($globalUser,$pages,$data,$controllerName) {
		$this->registerAppContextProperty("config", $GLOBALS['config']);
		$this->registerAppContextProperty("globalUser", $globalUser);
		$this->registerAppContextProperty("pages", $pages);
		$this->registerAppContextProperty("data", $data);
		$this->registerAppContextProperty("controllerName", $controllerName);
		$this->setViewPath('html');
		$this->setAdminViewPath('admin');
	}

	/**
	*	Displays the response as HTML
	*
	*/
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

	/**
	*	Sets the view tasked with presenting the page specific response
	*	@param string $viewFile The name of the view (This ViewRenderer will append '.view.php' to generate a filename
	*	@param boolean $isAdmin Define the view as admin or standard (determines the location of the viewFile)
	*	@return boolean Returns true if the file was found, false if not found
	*/
	public function setView($viewFile,$isAdmin=false) {
		$config = $this->getAppContextProperty("config");
		$fullPath = (($isAdmin) ? $this->getAdminViewPath():$this->getViewPath()).($this->getViewDirectory() ? "{$this->getViewDirectory()}/":'')."{$viewFile}.view.php";
		if (is_file($fullPath)) {
			$this->viewFile = $fullPath;
			return true;
		}
		return false;
	}

	/**
	*	Allows for defining a subdirectory for views which will be appended to the primary view path. 
	*	For example, all views for a Controller can be put in a view subdirectory, and that Controller will set the viewDirectory to match that location.
	*
	*/
	public function setViewDirectory($directoryName) {
		$this->viewDirectory = $directoryName;
	}

	/**
	*	Gets the (optional) subdirectory for views which will be appended to the primary view path. 
	*/
	protected function getViewDirectory() {
		return $this->viewDirectory;
	}

	/**
	*	@return string The $viewFile
	*/
	protected function getViewFile() {
		return $this->viewFile;
	}

	/**
	*	@return string The full directory path to the views
	*/
	protected function getViewPath() {
		return "{$this->getAppContextProperty('config')['PATH_VIEWS']}{$this->viewPath}/";
	}

	/**
	*	@param string $viewPath Sets the full directory path to the views
	*/
	protected function setViewPath($viewPath) {
		$this->viewPath = $viewPath;
	}

	/**
	*	@return string The full path to the admin views directory (by default, this is the same as the standard path)
	*/
	protected function getAdminViewPath() {
		return $this->getViewPath();
	}

	/**
	*	@param string Sets the admin view path, which is a subdirectory within the $viewPath
	*/
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

	/** 
 	*	Register a reference to a variable for the views geared toward the app environment (User session, server paths, config)
	*	@param string $name The name of the variable
	*	@param mixed $data The value of the variable
	*
	*/
	public function registerAppContextProperty($name,$data) {
		$this->appContext[$name] =& $data;
	}

	/**
	*	Returns the requested appContext variable
	*	@param string $name The name of the variable
	*	@return mixed|null Returns the variable or null if it was not found
	*/
	public function getAppContextProperty($name) {
		if (isset($this->appContext[$name])) {
			return $this->appContext[$name];
		}
		return null;
	}

	/*
	*	Registers the current page as an appContext variable
	*	@param Core\Interfaces\SitePage $page The current SitePage
	*/
	public function setPage($page) {
		$this->registerAppContextProperty("page",$page);
	}
}
?>