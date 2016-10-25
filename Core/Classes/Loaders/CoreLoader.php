<?php
namespace Core\Classes\Loaders;
use Core\Classes as CoreClasses;
use Core\Interfaces as CoreInterfaces;
use Core\Lib as CoreLib;

/**
*	The CoreLoader is the default implementation of the Loader interface.
*
*	The CoreLoader is responsible for:
* 		Starting the session
*		Preparing global vars for controller use
*		Managing an implementation of the Site class
*		Using the Site class to get the logged in User
*		Using the Site class to load appropriate controllers and render views
*	
*	@author Jason Savell <jsavell@library.tamu.edu>
*
*/

class CoreLoader implements CoreInterfaces\Loader {
	/** @var mixed[] $config The app configuration */
	private $config;
	/** @var Interfaces\Logger $logger A Logger implementation */
	private $logger;
	/** @var Interfaces\Site $site The Site context */
	private $site;

	public function __construct($config) {
		$this->config = $config;
		$this->logger = CoreLib\getLogger();
	}

	protected function setConfig($config) {
		$this->config = $config;
	}

	protected function getConfig() {
		return $this->config;
	}

	/**
	*	Gets the Site context
	*	@return Core\Interfaces\Site The active Site implementation
	*/
	protected function getSite() {
		return $this->site;
	}

	/**
	*	Sets the Site context
	*	@param Core\Interfaces\Site The active Site implementation
	*/
	protected function setSite($site) {
		$this->site = $site;
	}

	/**
	*	load() is responsible for taking us from the request to the rendered response.
	*	- Kick off the seesion
	*	- Honor any $config redirect requests
	*	- Hand execution over to a Controller
	*	- Render the view with a ViewRenderer
	*/
	public function load() {
		session_start();

		$this->loadSiteClass();
		$this->checkRedirect();
		$this->applyViewRenderer();

		$this->loadController();
		$this->render();

	}

	/**
	*	Check for and execute any $config requested redirects
	*
	*/
	protected function checkRedirect() {
		if (!empty($this->getConfig()['forceRedirectUrl'])) {
			$this->getSite()->setRedirectUrl("{$this->getConfig()['forceRedirectUrl']}");
			$this->getSite()->redirect();
		}
	}

	/**
	*	Looks for a configured Site implementation to utilize, falls back to CoreSite if none are found
	*/
	protected function loadSiteClass() {
		$site = null;
		$config = $this->getConfig();
		if (!empty($config['SITE_CLASS'])) {
			$className = "{$config['NAMESPACE_APP']}Classes\\{$config['SITE_CLASS']}";
			$site = new $className($config);
			$this->logger->debug("Loaded Configured Class: {$className}");
		} else {
			$site = new CoreClasses\CoreSite($config);
			$this->logger->debug("Loaded Core Site Class");
		}
		if (empty($site)) {
			$this->logger->error("Site Class not found");
			exit;
		}
		$this->setSite($site);
	}

	/**
	*	Finds an appropriate ViewRenderer and sets it up for use
	*/
	protected function applyViewRenderer() {
		//set the ViewRenderer
		$config = $this->getConfig();
		$inputData = $this->getSite()->getSanitizedInputData();
		$viewRendererFlag = false;
		if (!empty($inputData['json'])) {
			$this->getSite()->setViewRenderer(new CoreClasses\ViewRenderers\JSONViewRenderer());
			$viewRendererFlag = true;
		} else {
			if (!empty($config['VIEW_RENDERER'])) {
				if (class_exists("{$config['NAMESPACE_APP']}Classes\\ViewRenderers\\{$config['VIEW_RENDERER']}")) {
					$className = "{$config['NAMESPACE_APP']}Classes\\ViewRenderers\\{$config['VIEW_RENDERER']}";
				} elseif (class_exists("{$config['NAMESPACE_CORE']}Classes\\ViewRenderers\\{$config['VIEW_RENDERER']}")) {
					$className = "{$config['NAMESPACE_CORE']}Classes\\ViewRenderers\\{$config['VIEW_RENDERER']}";
				}
			}
			if (!$className) {
				$className = "{$config['NAMESPACE_CORE']}Classes\\ViewRenderers\\HTMLViewRenderer";
			}
			$this->getSite()->setViewRenderer(new $className($this->getSite()->getGlobalUser(),$this->getSite()->getPages(),$inputData,(!empty($config['controllerConfig']) ? $config['controllerConfig']['name']:null)));
			$viewRendererFlag = true;
		}
		if (!$viewRendererFlag) {
			$this->logger->error("ViewRenderer Class not found");
			exit;
		}
	}

	/**
	*	Looks for the configured Controller and hands over control by executing its evaluate() method
	*/
	protected function loadController() {
		//try to load the controller
		$config = $this->getConfig();
		$controller = null;
		if (!empty($config['controllerConfig']['name'])) {
			$className = $this->getSite()->getControllerClass($config['controllerConfig']['name']);
			if (class_exists($className)) {
				$controller = new $className($this->getSite(),$config['controllerConfig']);
				$controller->evaluate();
			}
		}
		if (!$controller) {
			$this->logger->warn("Did not find Controller Class");
			$this->getSite()->setRedirectUrl($config['PATH_HTTP']);
		}
	}

	/**
	*	Asks the ViewRenderer to render the response
	*/
	protected function render() {
		if ($this->getSite()->hasRedirectUrl()) {
			$this->getSite()->redirect();
		}
		//send system messages to the ViewRenderer
		$this->getSite()->getViewRenderer()->registerAppContextProperty("systemMessages", $this->getSite()->getSystemMessages());

		//display the content
		$this->getSite()->getViewRenderer()->renderView();
	}
}
?>