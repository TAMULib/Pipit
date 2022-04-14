<?php
namespace Pipit\Classes\Site;
use Pipit\Interfaces\Site;
use Pipit\Classes\CoreObject;
use Pipit\Classes\Exceptions\ConfigurationException;

/** 
*	An abstract implementation of the Site interface
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

abstract class AbstractSite extends CoreObject implements Site {
	use \Pipit\Traits\FileConfiguration;

	/** @var \Pipit\Interfaces\User $globalUser An array containing data about the logged in user */
	private $globalUser;
	/** @var mixed[] $siteConfig An array containing the site configuration */
	private $siteConfig;
	/** @var \Pipit\Interfaces\ViewRenderer A ViewRenderer implementation */
	private $viewRenderer;
	/** @var \Pipit\Interfaces\SitePage[] An array of SitePage */
	private $pages;
	/** @var \Pipit\Interfaces\SystemMessage[] An array of SystemMessage */
	protected $systemMessages;
	/** @var \Pipit\Interfaces\SitePage The currently requested SitePage */
	protected $currentPage;
	/** @var string[] $sanitizedInputData HTTP Request (GET, POST, etc..) data. */
	protected $sanitizedInputData;

	/**
	*	Returns the site configuration
	*	@return mixed[] $siteConfig
	*/
	public function getSiteConfig() {
		return $this->siteConfig;
	}

	/**
	*	Sets the site configuration
	*	@param mixed[] $siteConfig The site configuration
	*	@return void
	*/
	protected function setSiteConfig($siteConfig) {
		$this->siteConfig = $siteConfig;
	}

	/**
	*	Sets the logged in user
	*	@return void
	*/
	abstract protected function setUser();

	/**
	*	Set the pages for the Site.
	*
	*	@return void
	*/
	protected function setPages() {
		$pages = [];
		try {
			$pageConfig = $this->getConfigurationFromFileName("SitePages.config");
			if (count($pageConfig) > 0) {
				$pageLoadingError = false;
				foreach($pageConfig as $key=>$page) {
					if (is_array($page) && array_key_exists('name', $page) && array_key_exists('path', $page) && array_key_exists('accessLevel', $page)) {
						$pages[$key] = new CoreSitePage($page['name'],$page['path'],$page['accessLevel']);
					} else {
						$pageLoadingError = true;
					}
				}
				if ($pageLoadingError) {
					throw new ConfigurationException("Error loading site pages from configuration");
				}
			}
		} catch (\RuntimeException $e) {
			$this->getLogger()->debug("Error processing SitePages configuration");
		}
		$this->pages = $pages;
	}

	public function getPages() {
		return $this->pages;
	}

	/**
	*	Sets the currently requested page
	*	@param \Pipit\Interfaces\SitePage $page The currently requested SitePage
	*	@return void
	*/
	public function setCurrentPage($page) {
		$this->currentPage = $page;
	}

	/**
	*	Gets the currently requested page
	*	@return \Pipit\Interfaces\SitePage The currently requested SitePage
	*/
	public function getCurrentPage() {
		return $this->currentPage;
	}

	public function setViewRenderer($viewRenderer) {
		$this->viewRenderer = $viewRenderer;
	}

	public function getViewRenderer() {
		return $this->viewRenderer;
	}

	abstract public function setRedirectUrl($redirectUrl);
	abstract public function hasRedirectUrl();
	abstract public function redirect();

	abstract public function getControllerClass($controllerName);

	/**
	 * Generate safe input data from GET/POST/REQUEST
	 * Restricts basic repo actions (insert,remove,update) to POST
	 * Override this method to restrict additional app specific actions
	 * @return void
	 */
	public function generateSanitizedInputData() {
		$data = [];
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

	/**
	*	Set a representation of the application user associated with a request. 
	*	@param \Pipit\Interfaces\User $globalUser The application user
	*	@return void
	*/
	protected function setGlobalUser($globalUser) {
		$this->globalUser = $globalUser;
	}

	abstract public function addSystemMessage($message,$type="info");

	abstract public function getSystemMessages();

	/**
	*	Provides a uniform approach to fetching Interfaces\DataRepository
	*	Should handle all instantiation and any desired caching of repositories
	*	@param string $repositoryName The name of the desired Interfaces\DataRepository
	*	@return \Pipit\Interfaces\DataRepository|null
	*/
	abstract public function getDataRepository($repositoryName);

	/**
	*	Provides a uniform approach to fetching Helper service classes
	*	Should handle all instantiation and any desired caching of Helpers
	*	@param string $helperName The name of the desired Helper
	*	@return \Pipit\Classes\Helpers\AbstractHelper|null
	*/
	abstract public function getHelper($helperName);
}
