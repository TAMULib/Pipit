<?php
namespace Core\Classes;

/** 
*	The primary site manager
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class CoreSite extends AbstractSite {
	protected $cachedDataRepositories = array();

	public function __construct(&$siteConfig) {
		$this->setSiteConfig($siteConfig);
		$this->setPages($siteConfig['sitePages']);
		$this->generateSanitizedInputData();
		$this->setUser();
	}

	protected function setUser() {
		if (isset($this->getSiteConfig()['USECAS']) && $this->getSiteConfig()['USECAS']) {
			$this->setGlobalUser(new Data\UserCAS($this->getSanitizedInputData(),$this->getDataRepository('Users')));
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
						$this->getViewRenderer()->registerAppContextProperty("app_http", "{$this->getSiteConfig()['PATH_HTTP']}admin/{$currentPage->getPath()}/");
						$controllerClass = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Controllers\\".ucfirst($controllerName)."AdminController";
					} else {
						$this->getViewRenderer()->registerAppContextProperty("app_http", "{$this->getSiteConfig()['PATH_HTTP']}admin/");
						$controllerClass = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Controllers\\DefaultAdminController";
					}
				}
			} elseif ($this->getGlobalUser()->isLoggedIn() || $currentPage->getAccessLevel() == SECURITY_PUBLIC) {
				//load standard controller
				$this->getViewRenderer()->registerAppContextProperty("app_http", "{$this->getSiteConfig()['PATH_HTTP']}{$currentPage->getPath()}/");
				$controllerClass = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Controllers\\".ucfirst($controllerName)."Controller";
			}
		} else {
			$this->getViewRenderer()->registerAppContextProperty("app_http", "{$this->getSiteConfig()['PATH_HTTP']}");
			$controllerClass = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Controllers\\DefaultController";
		}
		return $controllerClass;
	}

	public function getDataRepository($repositoryName) {
		$repository = $this->getCachedDataRepository($repositoryName);
		if (!$repository) {
			$className = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Data\\{$repositoryName}";
			if (class_exists($className)) {
				$repository = new $className();
				$setSiteMethod = 'setSite';
				if (is_callable(array($repository,$setSiteMethod))) {
					$repository->$setSiteMethod($this);
				}
				$this->addCachedDataRepository($repositoryName,$repository);
				$this->getLogger()->debug("Providing FRESH Repo: ".$repositoryName);
			} else {
				$this->getLogger()->error("Could not find Repository: ".$repositoryName);
			}
		} else {
			$this->getLogger()->debug("Providing CACHED Repo: ".$repositoryName);
		}
		return $repository;
	}

	protected function addCachedDataRepository($repositoryName,$dataRepository) {
		$this->cachedDataRepositories[$repositoryName] = $dataRepository;
	}

	protected function getCachedDataRepository($repositoryName) {
		return array_key_exists($repositoryName, $this->cachedDataRepositories) ? $this->cachedDataRepositories[$repositoryName]:null;
	}

}
?>