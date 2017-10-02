<?php
namespace Core\Classes;

/** 
*	The primary site manager
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class CoreSite extends AbstractSite {
	/** @var Core\Interfaces\DataRepository[] $cachedDataRepositories A store of DataRepositories to provide (singletons) to requestors */
	protected $cachedDataRepositories = array();
	/** @var object[] $cachedHelpers A store of Helpers to provide (singletons) to requestors */
	protected $cachedHelpers = array();
	/** @var string $redirectUrl A url to be redirected to */
	private $redirectUrl = null;
	/** @var string $dynamicRepositoryKey The key to the location in $siteConfig of an array of DynamicRepositoryConfiguration */
	private $dynamicRepositoryKey;

	/**
	*	Constructs an instance of CoreSite
	*	@param mixed[] &$siteConfig A reference to the global site configuration
	*/
	public function __construct(&$siteConfig) {
		$this->setSiteConfig($siteConfig);
		$this->setPages($siteConfig['sitePages']);
		$this->generateSanitizedInputData();
		$this->setUser();
		$this->setDynamicRepositoryKey($siteConfig['DYNAMIC_REPOSITORY_KEY']);
	}

	/**
	*	Associates the session User with CoreSite
	*/
	protected function setUser() {
		if (!empty($this->getSiteConfig()['USER_CLASS'])) {
			$className = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Data\\{$this->getSiteConfig()['USER_CLASS']}";
			if (class_exists($className)) {
				$this->setGlobalUser(new $className());
			} else {
				$this->getLogger()->error("Configured User class not found: {$className}");
			}
		} else if (isset($this->getSiteConfig()['USECAS']) && $this->getSiteConfig()['USECAS']) {
			$this->setGlobalUser(new Data\UserCAS($this->getSanitizedInputData(),$this->getDataRepository('Users')));
		} else {
			$this->setGlobalUser(new Data\UserDB());
		}
	}

	public function addSystemMessage($message,$type="info") {
		$this->systemMessages[] = new CoreSystemMessage($message,$type);
	}

	/**
	*	Adds an error message to the systemMessages array
	*	@param string $message The error message
	*/
	public function addSystemError($message) {
		$this->addSystemMessage($message,'error');
	}

	public function getSystemMessages() {
		return $this->systemMessages;
	}

	/**
	*	Provides a fully namespaced Core\Interfaces\Controller class name based on a Controller string name
	*	Ex. The string 'Widgets' will result in a Controller class name of NAMESPACE_APP\\Classes\\Controllers\\(WidgetsController,WidgetsAdminController)
	*
	*	If no controller is found for the given controller name, the method will fall back to either DefaultController or DefaultAdminController
	*
	*	@param string $controllerName The name of the desired controller
	*	@return Core\Interfaces\Controller
	*
	*/
	public function getControllerClass($controllerName) {
		$controllerClass = null;

		if (array_key_exists($controllerName,$this->getPages()) || $controllerName == 'user') {
			if ($controllerName == 'user') {
				$this->setCurrentPage(new CoreSitePage('user','user',SECURITY_PUBLIC));
			} elseif ($this->getPages()[$controllerName]) {
				$this->setCurrentPage($this->getPages()[$controllerName]);
			}
			$currentPage = $this->getCurrentPage();
			if ($currentPage->isAdminPage()) {
				//if the user is an admin, load the admin controller, otherwise, return false;
				if ($this->getGlobalUser()->isAdmin()) {
					if ($controllerName) {
						$this->getViewRenderer()->registerAppContextProperty("app_http", "{$this->getSiteConfig()['PATH_HTTP']}{$currentPage->getPath()}/");
						$controllerClass = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Controllers\\".ucfirst($controllerName)."AdminController";
					} else {
						$this->getViewRenderer()->registerAppContextProperty("app_http", "{$this->getSiteConfig()['PATH_HTTP']}");
						$controllerClass = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Controllers\\DefaultAdminController";
					}
				}
			} elseif ($this->getGlobalUser()->isLoggedIn() || $currentPage->isPublicPage()) {
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

	/**
	*	Provides a unified source for DataRepository singletons in response to requests for a DataRepository
	*	@param string $repositoryName The name of the desired DataRepository
	*	@return Core\Interfaces\DataRepository|null The resulting DataRepository if found, null otherwise
	*
	*/
	public function getDataRepository($repositoryName) {
		//first check if we've already instantiated this DataRepository
		$repository = $this->getCachedDataRepository($repositoryName);
		if (!$repository) {
			if (is_array($this->getSiteConfig()[$this->getDynamicRepositoryKey()]) && array_key_exists($repositoryName,$this->getSiteConfig()[$this->getDynamicRepositoryKey()])) {
				$repository = new Data\DynamicDatabaseRepository($this->getSiteConfig()[$this->getDynamicRepositoryKey()][$repositoryName]);
				$this->addCachedDataRepository($repositoryName,$repository);
			} else {
				$className = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Data\\{$repositoryName}";
				//We found a DataRepository named $repositoryName, so let's instantiate, configure and cache it
				if (class_exists($className)) {
					$repository = new $className();
					$repository->configure($this);
					$this->addCachedDataRepository($repositoryName,$repository);
				}
			}
			if (!$repository) {
				$this->getLogger()->error("Could not find Repository: ".$repositoryName);
			} else {
				$this->getLogger()->debug("Providing FRESH Repo: ".$repositoryName);
			}
		} else {
			$this->getLogger()->debug("Providing CACHED Repo: ".$repositoryName);
		}
		return $repository;
	}

	/**
	*	Adds a new DataRepository to the list of previously requested DataRepositories
	*	@param string $repositoryName The name of the DataRepository
	*	@param Core\Interfaces\DataRepository An instance of a DataRepository implementation
	*/
	protected function addCachedDataRepository($repositoryName,$dataRepository) {
		$this->cachedDataRepositories[$repositoryName] = $dataRepository;
	}

	/**
	*	Fetch a DataRepository from the cache by its name
	*	@param string $repositoryName The name of the desired DataRepository
	*	@return Core\Interfaces\DataRepository|null 
	*/
	protected function getCachedDataRepository($repositoryName) {
		return array_key_exists($repositoryName, $this->cachedDataRepositories) ? $this->cachedDataRepositories[$repositoryName]:null;
	}

	/**
	*	Fetch a Helper from the cache by its name
	*	@param string $helperName The name of the desired Helper
	*	@return object|null 
	*/
	public function getHelper($helperName) {
		//first check if we've already instantiated this Helper
		$helper = $this->getCachedHelper($helperName);
		if (!$helper) {
			foreach (array($this->getSiteConfig()['NAMESPACE_APP'],$this->getSiteConfig()['NAMESPACE_CORE']) as $classPath) {
				$className = "{$classPath}Classes\\Helpers\\{$helperName}";
				//We found a Helper named $helperName, so let's instantiate, configure and cache it
				if (class_exists($className)) {
					$helper = new $className();
					$helper->configure($this);
					$this->addCachedHelper($helperName,$helper);
					$this->getLogger()->debug("Providing FRESH Helper: ".$helperName);
					break;
				}
			}
			if (!$helper) {
				$this->getLogger()->error("Could not find Helper: ".$helperName);
			}
		} else {
			$this->getLogger()->debug("Providing CACHED Helper: ".$helperName);
		}
		return $helper;
	}

	protected function addCachedHelper($helperName,$helperInstance) {
		$this->cachedHelpers[$helperName] = $helperInstance;
	}

	protected function getCachedHelper($helperName) {
		return array_key_exists($helperName, $this->cachedHelpers) ? $this->cachedHelpers[$helperName]:null;
	}

	public function setRedirectUrl($redirectUrl) {
		$this->redirectUrl = $redirectUrl;
	}

	public function hasRedirectUrl() {
		return !empty($this->redirectUrl);
	}


	public function redirect() {
		$this->getLogger()->debug("REDIRECTING TO: {$this->redirectUrl}");
		header("Location: {$this->redirectUrl}");
		exit;
	}

	protected function getDynamicRepositoryKey() {
		return $this->dynamicRepositoryKey;
	}

	protected function setDynamicRepositoryKey($dynamicRepositoryKey) {
		$this->dynamicRepositoryKey = $dynamicRepositoryKey;
	}
}
?>