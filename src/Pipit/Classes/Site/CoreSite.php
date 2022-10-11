<?php
namespace Pipit\Classes\Site;
use Pipit\Classes\Data as Data;
use Pipit\Classes\Configuration\DynamicDatabaseRepositoryConfiguration;
use Pipit\Classes\Exceptions\ConfigurationException;

/**
*	The primary site manager
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class CoreSite extends AbstractSite {
    /** @var \Pipit\Interfaces\DataRepository[] $cachedDataRepositories A store of DataRepositories to provide (singletons) to requestors */
    protected $cachedDataRepositories = array();
    /** @var \Pipit\Classes\Helpers\AbstractHelper[] $cachedHelpers A store of Helpers to provide (singletons) to requestors */
    protected $cachedHelpers = array();
    /** @var string $redirectUrl A url to be redirected to */
    private $redirectUrl = null;

    private const DYNAMIC_REPO_CONFIG_FILE = "dynamic.db.repositories";

    /**
    *	Constructs an instance of CoreSite
    *	@param mixed[] &$siteConfig A reference to the global site configuration
    */
    public function __construct(&$siteConfig) {
        $this->setSiteConfig($siteConfig);
        $this->setPages();
        $this->generateSanitizedInputData();
        $this->setUser();
    }

    /**
    *	Associates the session User with CoreSite
    *	@return void
    */
    protected function setUser() {
        $config = $this->getSiteConfig();
        $userClass = null;
        if (is_array($config)) {
            if (array_key_exists('USER_CLASS', $config) && is_string($config['USER_CLASS']) && is_string($config['NAMESPACE_APP'])) {
                $className = "{$config['NAMESPACE_APP']}Classes\\Data\\{$config['USER_CLASS']}";
                if (class_exists($className)) {
                    $userClass = new $className();
                    if (!($userClass instanceof \Pipit\Interfaces\User)) {
                        $userClass = null;
                        throw new ConfigurationException("Configured User class does not implement: Pipit\Interfaces\User");
                    }
                } else {
                    throw new ConfigurationException("Configured User class not found: {$className}");
                }
            }
            if ($userClass && is_bool($config['USECAS']) && $config['USECAS']) {
                if ($config['CAS_USER_REPO']) {
                    $userRepo = $this->getDataRepository($config['CAS_USER_REPO']);
                    if ($userRepo instanceof \Pipit\Interfaces\DataRepository) {
                        if ($userClass instanceof \Pipit\Classes\Data\UserCAS) {
                            $userClass = new Data\UserCAS($this->getSanitizedInputData(),$userRepo);
                            unset($userRepo);
                        } else {
                            $userClass = null;
                            throw new ConfigurationException("Configured UserCAS classes must extend Pipit\Classes\Data\UserCAS");
                        }
                    } else {
                        throw new ConfigurationException("UserCAS requires a Pipit\Interfaces\DataRepository");
                    }
                } else {
                    throw new ConfigurationException("UserCAS requires CAS_USER_REPO to be defined with a Pipit\Interfaces\DataRepository");
                }
            }
        }
        if (!$userClass) {
            $userClass = new Data\UserDB();
        }
        $this->setGlobalUser($userClass);
    }

    public function addSystemMessage($message,$type="info") {
        $this->systemMessages[] = new CoreSystemMessage($message,$type);
    }

    /**
    *	Adds an error message to the systemMessages array
    *	@param string $message The error message
    *	@return void
    */
    public function addSystemError($message) {
        $this->addSystemMessage($message,'error');
    }

    public function getSystemMessages() {
        return $this->systemMessages;
    }

    /**
    *	Provides a fully namespaced Pipit\Interfaces\Controller class name based on a Controller string name
    *	Ex. The string 'Widgets' will result in a Controller class name of NAMESPACE_APP\\Classes\\Controllers\\(WidgetsController,WidgetsAdminController)
    *
    *	If no controller is found for the given controller name, the method will attempt to fall back
    *   to either DefaultController or DefaultAdminController provided by client
    *
    *	@param string $controllerName The name of the desired controller
    *	@return string|null
    */
    public function getControllerClass($controllerName) {
        $controllerClass = null;
        $config = $this->getSiteConfig();
        if (is_array($config) && is_string($config['NAMESPACE_APP']) && is_string($config['PATH_HTTP'])) {
            if (array_key_exists($controllerName,$this->getPages()) || $controllerName == 'user') {
                if ($controllerName == 'user') {
                    $this->setCurrentPage(new CoreSitePage('user','user',SECURITY_PUBLIC));
                } elseif (array_key_exists($controllerName, $this->getPages())) {
                    $this->setCurrentPage($this->getPages()[$controllerName]);
                }
                $currentPage = $this->getCurrentPage();
                if ($currentPage->isAdminPage()) {
                    //if the user is an admin, load the admin controller, otherwise, return false;
                    if ($this->getGlobalUser()->isAdmin()) {
                        /** @todo This conditional no longer makes sense.  */
                        if ($controllerName) {
                            $this->getViewRenderer()->registerAppContextProperty("app_http", "{$config['PATH_HTTP']}{$currentPage->getPath()}/");
                            $controllerClass = "{$config['NAMESPACE_APP']}Classes\\Controllers\\".ucfirst($controllerName)."AdminController";
                        } else {
                            $this->getViewRenderer()->registerAppContextProperty("app_http", "{$config['PATH_HTTP']}");
                            $controllerClass = "{$config['NAMESPACE_APP']}Classes\\Controllers\\DefaultAdminController";
                        }
                    }
                } elseif ($this->getGlobalUser()->isLoggedIn() || $currentPage->isPublicPage()) {
                    //load standard controller
                    $this->getViewRenderer()->registerAppContextProperty("app_http", "{$config['PATH_HTTP']}{$currentPage->getPath()}/");
                    $controllerClass = "{$config['NAMESPACE_APP']}Classes\\Controllers\\".ucfirst($controllerName)."Controller";
                }
            } else {
                $this->getViewRenderer()->registerAppContextProperty("app_http", "{$config['PATH_HTTP']}");
                $controllerClass = "{$config['NAMESPACE_APP']}Classes\\Controllers\\DefaultController";
            }
        }
        return $controllerClass;
    }

    /**
    *	Provides a unified source for DataRepository singletons in response to requests for a DataRepository
    *	@param string $repositoryName The name of the desired DataRepository
    *	@return \Pipit\Interfaces\DataRepository|null The resulting DataRepository if found, null otherwise
    */
    public function getDataRepository($repositoryName) {
        //first check if we've already instantiated this DataRepository
        $repository = $this->getCachedDataRepository($repositoryName);
        if (!$repository) {
            $dynamicConfigFile = self::DYNAMIC_REPO_CONFIG_FILE;
            $foundRepository = false;
            if ($this->configurationFileExists($dynamicConfigFile)) {
                $dynamicRepoConfigData = $this->getConfigurationFromFileName($dynamicConfigFile);
                if (is_array($dynamicRepoConfigData) && array_key_exists($repositoryName, $dynamicRepoConfigData)) {
                    $config = $dynamicRepoConfigData[$repositoryName];
                    $dynamicRepoConfig = new DynamicDatabaseRepositoryConfiguration(
                                        $config['tableName'],
                                        $config['primaryKey'],
                                        $config['defaultOrderBy'],
                                        $config['gettableColumns'],
                                        $config['searchableColumns']
                                    );
                    $this->addCachedDataRepository($repositoryName, new Data\DynamicDataBaseRepository($dynamicRepoConfig));
                    $foundRepository = true;
                }
            }
            if (!$foundRepository) {
                if (is_string($this->getSiteConfig()['NAMESPACE_APP'])) {
                    $className = "{$this->getSiteConfig()['NAMESPACE_APP']}Classes\\Data\\{$repositoryName}";
                    //We found a DataRepository named $repositoryName, so let's instantiate, configure and cache it
                    if (class_exists($className)) {
                        $repository = new $className();
                        if (!($repository instanceof \Pipit\Interfaces\DataRepository)) {
                            $repository = null;
                        } elseif ($repository instanceof \Pipit\Interfaces\Configurable) {
                            $repository->configure($this);
                            $this->addCachedDataRepository($repositoryName,$repository);
                            $foundRepository = true;
                        } else {
                            $repository = null;
                            throw new \RuntimeException("Repositories must implement Pipit\Interfaces\Configurable: ".$repositoryName);
                        }
                    }
                }
            }
            if (!$foundRepository) {
                throw new \RuntimeException("Could not find valid Repository: ".$repositoryName);
            } else {
                $this->getLogger()->debug("Providing FRESH Repo: ".$repositoryName);
                $repository = $this->getCachedDataRepository($repositoryName);
            }
        } else {
            $this->getLogger()->debug("Providing CACHED Repo: ".$repositoryName);
        }
        return $repository;
    }

    /**
    *	Adds a new DataRepository to the list of previously requested DataRepositories
    *	@param string $repositoryName The name of the DataRepository
    *	@param \Pipit\Interfaces\DataRepository $dataRepository An instance of a DataRepository implementation
    *	@return void
    */
    protected function addCachedDataRepository($repositoryName,$dataRepository) {
        $this->cachedDataRepositories[$repositoryName] = $dataRepository;
    }

    /**
    *	Fetch a DataRepository from the cache by its name
    *	@param string $repositoryName The name of the desired DataRepository
    *	@return \Pipit\Interfaces\DataRepository|null
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
                if (is_string($classPath)) {
                    $className = "{$classPath}Classes\\Helpers\\{$helperName}";
                    //We found a Helper named $helperName, so let's instantiate, configure and cache it
                    if (class_exists($className)) {
                        $helper = new $className();
                        if ($helper instanceof \Pipit\Classes\Helpers\AbstractHelper) {
                            $helper->configure($this);
                            $this->addCachedHelper($helperName,$helper);
                            $this->getLogger()->debug("Providing FRESH Helper: ".$helperName);
                            break;
                        } else {
                            $helper = null;
                            throw new \RuntimeException("Helpers must extend Pipit\Classes\Helpers\AbstractHelper: ".$helperName);
                        }
                    }
                }
            }
            if (!$helper) {
                throw new \RuntimeException("Could not find valid Helper: ".$helperName);
            }
        } else {
            $this->getLogger()->debug("Providing CACHED Helper: ".$helperName);
        }
        return $helper;
    }

    /**
     * Adds a Helper instance to the cache
     * @param string $helperName The name of the Helper instance
     * @param \Pipit\Classes\Helpers\AbstractHelper $helperInstance The Helper instance to cache
     * @return void
     */
    protected function addCachedHelper($helperName,$helperInstance) {
        $this->cachedHelpers[$helperName] = $helperInstance;
    }

    /**
     * @param string $helperName The name of the helper to retrieve from the cache
     * @return \Pipit\Classes\Helpers\AbstractHelper|null
     */
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
}
