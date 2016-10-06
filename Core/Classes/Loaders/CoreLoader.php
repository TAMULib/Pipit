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
	private function checkRedirect() {
		if (!empty($this->getConfig()['forceRedirectUrl'])) {
			$this->site->setRedirectUrl("{$this->getConfig()['forceRedirectUrl']}");
			$this->site->redirect();
		}
	}

	/**
	*	Looks for a configured Site implementation to utilize, falls back to CoreSite if none are found
	*/
	private function loadSiteClass() {
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
		$this->site = $site;
	}

	/**
	*	Finds an appropriate ViewRenderer and sets it up for use
	*/
	private function applyViewRenderer() {
		//set the ViewRenderer
		$config = $this->getConfig();
		$inputData = $this->site->getSanitizedInputData();
		$viewRendererFlag = false;
		if (!empty($inputData['json'])) {
			$this->site->setViewRenderer(new CoreClasses\ViewRenderers\JSONViewRenderer());
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
			$this->site->setViewRenderer(new $className($this->site->getGlobalUser(),$this->site->getPages(),$inputData,$config['controllerConfig']['name']));
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
	private function loadController() {
		//try to load the controller
		$config = $this->getConfig();
		$className = $this->site->getControllerClass($config['controllerConfig']['name']);
		if (class_exists($className)) {
			$controller = new $className($this->site,$config['controllerConfig']);
			$controller->evaluate();
		} else {
			$this->logger->warn("Did not find Controller Class");
			$this->site->setRedirectUrl($config['PATH_HTTP']);
		}
	}

	/**
	*	Asks the ViewRenderer to render the response
	*/
	private function render() {
		if ($this->site->hasRedirectUrl()) {
			$this->site->redirect();
		}
		//send system messages to the ViewRenderer
		$this->site->getViewRenderer()->registerAppContextProperty("systemMessages", $this->site->getSystemMessages());

		//display the content
		$this->site->getViewRenderer()->renderView();
	}
}
?>