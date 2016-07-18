<?php
namespace App\Classes;
use Core\Classes as CoreClasses;
/** 
*	The primary site manager
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class Site extends CoreClasses\AbstractSite {

	public function addSystemMessage($message,$type="info") {
		$this->systemMessages[] = new SystemMessage($message,$type);
	}

	public function addSystemError($message) {
		$this->addSystemMessage($message,'error');
	}

	public function getSystemMessages() {
		return $this->systemMessages;
	}

	public function getControllerClass($controllerName) {
		$controllerClass = null;

		if (array_key_exists($controllerName,$this->getPages()) || $controllerName == 'user') {
			if ($controllerName == 'user') {
				$this->setCurrentPage(new SitePage('user','user',SECURITY_PUBLIC));
			} else {
				$this->setCurrentPage($this->getPages()[$controllerName]);
			}
			$currentPage = $this->getCurrentPage();
			if ($currentPage->getAccessLevel() == SECURITY_ADMIN) {
				//if the user is an admin, load the admin controller, otherwise, return false;
				if ($this->getGlobalUser()->isAdmin()) {
					if ($controllerName) {
						$this->getViewRenderer()->registerAppContextProperty("app_http", "{$this->getSiteConfig()['PATH_HTTP']}admin/{$controllerName}/");
						$controllerClass = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Controllers\\".ucfirst($controllerName)."AdminController";
					} else {
						$this->getViewRenderer()->registerAppContextProperty("app_http", "{$this->getSiteConfig()['PATH_HTTP']}admin/");
						$controllerClass = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Controllers\\DefaultAdminController";
					}
				}
			} elseif ($this->getGlobalUser()->isLoggedIn() || $currentPage->getAccessLevel() == SECURITY_PUBLIC) {
				//load standard controller
				$this->getViewRenderer()->registerAppContextProperty("app_http", "{$this->getSiteConfig()['PATH_HTTP']}{$controllerName}/");
				$controllerClass = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Controllers\\".ucfirst($controllerName)."Controller";
			}
		} else {
			$this->getViewRenderer()->registerAppContextProperty("app_http", "{$this->getSiteConfig()['PATH_HTTP']}");
			$controllerClass = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Controllers\\DefaultController";
		}
		return $controllerClass;

	}

	public function getDataRepository($repositoryName) {
		$className = __NAMESPACE__.'\\Data\\'.$repositoryName;
		if (class_exists($className)) {
			return new $className();
		}
		$this->getLogger()->error("Could not find Repository: ".$repositoryName);
		return null;
	}
}