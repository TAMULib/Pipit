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
		if ($viewRendererName = $this->getViewRendererName()) {
			$this->getSite()->setViewRenderer(new $viewRendererName($this->getSite()->getGlobalUser(),$this->getSite()->getPages(),$inputData,(!empty($config['controllerConfig']) ? $config['controllerConfig']['name']:null)));
		} else {
			$this->logger->error("ViewRenderer Class not found");
			exit;
		}
	}

	/**
	*	Provides the fully qualified class name of the ViewRenderer that should be used to render the response
	*	App level extenders of CoreLoader can override this method to use their own criteria to select the ViewRenderer
	*	@return string $viewRendererName - The fully qualified class name of the ViewRenderer to be used to render the response
	*/
	protected function getViewRendererName() {
		$config = $this->getConfig();
		$inputData = $this->getSite()->getSanitizedInputData();
		$viewRendererName = null;

		$availableCoreRenderers = array("json","csv","html");

		$viewRenderOverride = null;

		//legacy support for original GET request of JSONViewRenderer
		if (!empty($inputData['json'])) {
			$viewRenderOverride = "JSONViewRenderer";
		} else if (!empty($inputData['view_renderer']) && in_array($inputData['view_renderer'],$availableCoreRenderers)) {
			$viewRenderOverride = "{$inputData['view_renderer']}ViewRenderer";
		}
		if ($viewRenderOverride) {
			$viewRendererName = "{$config['NAMESPACE_CORE']}Classes\\ViewRenderers\\{$viewRenderOverride}";
		} else if (!empty($config['VIEW_RENDERER'])) {
			if (class_exists("{$config['NAMESPACE_APP']}Classes\\ViewRenderers\\{$config['VIEW_RENDERER']}")) {
				$viewRendererName = "{$config['NAMESPACE_APP']}Classes\\ViewRenderers\\{$config['VIEW_RENDERER']}";
			} elseif (class_exists("{$config['NAMESPACE_CORE']}Classes\\ViewRenderers\\{$config['VIEW_RENDERER']}")) {
				$viewRendererName = "{$config['NAMESPACE_CORE']}Classes\\ViewRenderers\\{$config['VIEW_RENDERER']}";
			}
		} else {
			$viewRendererName = "{$config['NAMESPACE_CORE']}Classes\\ViewRenderers\\HTMLViewRenderer";
		}
		return $viewRendererName;
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
				$site = $this->getSite();
				$controller = new $className($site, $config['controllerConfig']);
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

