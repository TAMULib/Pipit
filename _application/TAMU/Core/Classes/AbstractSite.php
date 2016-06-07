<?php
namespace TAMU\Core\Classes;
use TAMU\Core\Interfaces as Interfaces;
/** 
*	An abstract implementation of the Site interface
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

abstract class AbstractSite implements Interfaces\Site {
	private $globalUser;
	private $siteConfig;
	private $viewRenderer;
	private $pages;
	private $inputData;

	public function __construct(&$siteConfig,$pages) {
		$this->siteConfig = $siteConfig;
		$this->setPages($pages);
		$this->generateSanitizedInputData();
		$this->setUser();
	}

	protected function setUser() {
		//build the user
		if (isset($this->siteConfig['USECAS']) && $this->siteConfig['USECAS']) {
			$this->globalUser = new Data\UserCAS();
			$casTicket = $this->getInputData()['ticket'];
			if (!empty($casTicket)) {
				if ($this->globalUser->processLogIn($casTicket)) {
					header("Location:{$this->siteConfig['PATH_HTTP']}");
				}
			} elseif (!$this->getGlobalUser()->isLoggedIn() && !isset($this->getInputData()['action'])) {
				$this->getGlobaluser()->initiateLogIn();
			}
		} else {
			$this->globalUser = new Data\User();
		}
	}

	public function setPages($pages) {
		$this->pages = $pages;
	}

	public function getPages() {
		return $this->pages;
	}

	public function setViewRenderer($viewRenderer) {
		$this->viewRenderer = $viewRenderer;
	}

	public function getViewRenderer() {
		return $this->viewRenderer;
	}

	public function getControllerPath($controllerName) {
		$controllerPath = null;
		//load admin controller if user is logged in and an admin page
		if (array_key_exists($controllerName,$this->pages) || $controllerName == 'user') {
			if (!empty($this->pages[$controllerName]['admin']) && $this->pages[$controllerName]['admin'] == true) {
				//if the user is an admin, load the admin controller, otherwise, return false;
				if ($this->globalUser->isAdmin()) {
					if ($controllerName) {
						$this->viewRenderer->registerAppContextProperty("app_http", "{$this->siteConfig['PATH_HTTP']}admin/{$controllerName}/");
						$controllerPath = "{$this->siteConfig['PATH_CONTROLLERS']}admin/{$controllerName}.control.php";
					} else {
						$this->viewRenderer->registerAppContextProperty("app_http", "{$this->siteConfig['PATH_HTTP']}admin/");
						$controllerPath = "{$this->siteConfig['PATH_CONTROLLERS']}admin/default.control.php";
					}
				}
			} elseif ($this->globalUser->isLoggedIn() || (!$this->globalUser->isLoggedIn() && $controllerName == 'user')) {
				//load standard controller
				$this->viewRenderer->registerAppContextProperty("app_http", "{$this->siteConfig['PATH_HTTP']}{$controllerName}/");

				$controllerPath = "{$this->siteConfig['PATH_CONTROLLERS']}{$controllerName}.control.php";
			}
		} else {
			$this->viewRenderer->registerAppContextProperty("app_http", "{$this->siteConfig['PATH_HTTP']}");
			$controllerPath = "{$this->siteConfig['PATH_CONTROLLERS']}default.control.php";
		}
		return $controllerPath;
	}

	protected function generateSanitizedInputData() {
		if (!empty($_GET['action'])) {
			//restrict any controller actions that alter DB data to POST
			$restrictedActions = array("insert","remove","update");
			if (!in_array($_GET['action'],$restrictedActions)) {
				$data = $_GET;
			}
		} elseif (!empty($_POST['action'])) {
			$data = $_POST;
		} else {
			$data = $_REQUEST;
		}
		$this->sanitizedInputData = $data;
	}

	public function getSanitizedInputData() {
		return $this->sanitizedInputData;
	}

	public function getGlobalUser() {
		return $this->globalUser;
	}
}