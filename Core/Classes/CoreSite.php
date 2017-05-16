<?php
namespace Core\Classes;

/** 
*	The primary site manager
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class CoreSite extends AbstractSite {
	/** @var Core\Interfaces\DataRepository[] $cachedDataRepositories A store of DataRepositories to provide (singletons) to requestors */
	protected $cachedDataRepositories = array();
	private $redirectUrl = null;
	private $simpleRepositoryKey;

	/**
	*	Constructs an instance of CoreSite
	*	@param mixed[] &$siteConfig A reference to the global site configuration
	*/
	public function __construct(&$siteConfig) {
		$this->setSiteConfig($siteConfig);
		$this->setPages($siteConfig['sitePages']);
		$this->generateSanitizedInputData();
		$this->setUser();
		$this->setSimpleRepositoryKey($siteConfig['SIMPLE_REPOSITORY_KEY']);
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
			if (is_array($this->getSiteConfig()[$this->getSimpleRepositoryKey()]) && array_key_exists($repositoryName,$this->getSiteConfig()[$this->getSimpleRepositoryKey()])) {
				$repositoryConfig = $this->getSiteConfig()[$this->getSimpleRepositoryKey()][$repositoryName];
				$repository = new Data\SimpleDatabaseRepository($repositoryConfig->getTableName(),$repositoryConfig->getPrimaryKey(),$repositoryConfig->getDefaultOrderBy(),$repositoryConfig->getGettableColumns(),$repositoryConfig->getSearchableColumns());
				$this->addCachedDataRepository($repositoryName,$repository);
			} else {
				$className = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Data\\{$repositoryName}";
				//We found a DataRepository named $repositoryName, so let's instantiate, configure and cache it
				if (class_exists($className)) {
					$repository = new $className();
					$setSiteMethod = 'setSite';
					//provides the CoreSite instance to DataRepositories that have asked for it.
					if (is_callable(array($repository,$setSiteMethod))) {
						$repository->$setSiteMethod($this);
					}
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

	protected function getSimpleRepositoryKey() {
		return $this->simpleRepositoryKey;
	}

	protected function setSimpleRepositoryKey($simpleRepositoryKey) {
		$this->simpleRepositoryKey = $simpleRepositoryKey;
	}
}
?>