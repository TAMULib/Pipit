<?php
namespace Core\Classes;

/** 
*	The primary site manager
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class CoreSite extends AbstractSite {

	public function __construct(&$siteConfig) {
		$this->setSiteConfig($siteConfig);
		$this->setPages($siteConfig['sitePages']);
		$this->generateSanitizedInputData();
		$this->setUser();
	}

	protected function setUser() {
		//build the user
		if (isset($this->siteConfig['USECAS']) && $this->siteConfig['USECAS']) {
			$this->setGlobalUser(new AppData\UserCAS());
			$casTicket = $this->getSanitizedInputData()['ticket'];
			if (!empty($casTicket)) {
				if ($this->globalUser->processLogIn($casTicket)) {
					header("Location:{$this->siteConfig['PATH_HTTP']}");
				}
			} elseif (!$this->getGlobalUser()->isLoggedIn() && !isset($this->getSanitizedInputData()['action'])) {
				$this->getGlobaluser()->initiateLogIn();
			}
		} else {
			$this->setGlobalUser(new Data\User());
		}
	}

	public function addSystemMessage($message,$type="info") {
		$this->systemMessages[] = new CoreSystemMessage($message,$type);
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
				$this->setCurrentPage(new CoreSitePage('user','user',SECURITY_PUBLIC));
			} elseif ($this->getPages()[$controllerName]) {
				$this->setCurrentPage($this->getPages()[$controllerName]);
			}
			$currentPage = $this->getCurrentPage();
			if ($currentPage->getAccessLevel() == SECURITY_ADMIN) {
				//if the user is an admin, load the admin controller, otherwise, return false;
				if ($this->getGlobalUser()->isAdmin()) {
					if ($controllerName) {
						$this->getViewRenderer()->registerAppContextProperty("app_http", "{$this->getSiteConfig()['PATH_HTTP']}admin/{$this->getPages()[$controllerName]->getPath()}/");
						$controllerClass = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Controllers\\".ucfirst($controllerName)."AdminController";
					} else {
						$this->getViewRenderer()->registerAppContextProperty("app_http", "{$this->getSiteConfig()['PATH_HTTP']}admin/");
						$controllerClass = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Controllers\\DefaultAdminController";
					}
				}
			} elseif ($this->getGlobalUser()->isLoggedIn() || $currentPage->getAccessLevel() == SECURITY_PUBLIC) {
				//load standard controller
				$this->getViewRenderer()->registerAppContextProperty("app_http", "{$this->getSiteConfig()['PATH_HTTP']}{$this->getPages()[$controllerName]->getPath()}/");
				$controllerClass = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Controllers\\".ucfirst($controllerName)."Controller";
			}
		} else {
			$this->getViewRenderer()->registerAppContextProperty("app_http", "{$this->getSiteConfig()['PATH_HTTP']}");
			$controllerClass = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Controllers\\DefaultController";
		}
		return $controllerClass;
	}

	public function getDataRepository($repositoryName) {
		$className = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Data\\{$repositoryName}";
		if (class_exists($className)) {
			return new $className();
		}
		$this->getLogger()->error("Could not find Repository: ".$repositoryName);
		return null;
	}

}
?>