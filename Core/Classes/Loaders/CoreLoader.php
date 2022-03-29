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
	/** @var \Core\Interfaces\Logger $logger A Logger implementation */
	private $logger;
	/** @var \Core\Interfaces\Site $site The Site context */
	private $site;

	/**
	 * @param mixed[] $config The app configuration
	 */
	public function __construct($config) {
		$this->config = $config;
		$this->logger = CoreLib\getLogger();
	}

	/**
	 * @param mixed[] $config The app configuration
	 * @return void
	 */
	protected function setConfig($config) {
		$this->config = $config;
	}

	/**
	 * @return mixed[]
	 */
	protected function getConfig() {
		return $this->config;
	}

	/**
	*	Gets the Site context
	*	@return \Core\Interfaces\Site The active Site implementation
	*/
	protected function getSite() {
		return $this->site;
	}

	/**
	*	Sets the Site context
	*	@param \Core\Interfaces\Site $site The active Site implementation
	*	@return void
	*/
	protected function setSite(\Core\Interfaces\Site $site) {
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
	*	@return void
	*/
	protected function checkRedirect() {
		if (!empty($this->getConfig()['forceRedirectUrl'])) {
			$this->getSite()->setRedirectUrl("{$this->getConfig()['forceRedirectUrl']}");
			$this->getSite()->redirect();
		}
	}

	/**
	*	Looks for a configured Site implementation to utilize, falls back to CoreSite if none are found
	*	@return void
	*/
	protected function loadSiteClass() {
		$site = null;
		$config = $this->getConfig();

		if (!empty($config['SITE_CLASS'])) {
			$className = "{$config['NAMESPACE_APP']}Classes\\{$config['SITE_CLASS']}";
			$site = new $className($config);

			$potentialSiteClass = new $className($config);
			if ($potentialSiteClass instanceof \Core\Interfaces\Site) {
				$this->setSite($potentialSiteClass);
				unset($potentialSiteClass);
			}
			$this->logger->debug("Loaded Configured Class: {$className}");
		}
		if (!($this->getSite() instanceof \Core\Interfaces\Site)) {
			$coreSite = new CoreClasses\CoreSite($config);
			if ($coreSite instanceof \Core\Interfaces\Site) {
				$this->setSite($coreSite);
			}
			$this->logger->debug("Loaded Core Site Class");
		}
	}

	/**
	*	Finds an appropriate ViewRenderer and sets it up for use
	*	@return void
	*/
	protected function applyViewRenderer() {
		//set the ViewRenderer
		$config = $this->getConfig();
		$inputData = $this->getSite()->getSanitizedInputData();
		$hasViewRenderer = false;
		if ($viewRendererName = $this->getViewRendererName()) {
			$potentialViewRenderer = new $viewRendererName($this->getSite()->getGlobalUser(),$this->getSite()->getPages(),$inputData,(!empty($config['controllerConfig']) ? $config['controllerConfig']['name']:null));
			if ($potentialViewRenderer instanceof \Core\Interfaces\ViewRenderer) {
				$this->getSite()->setViewRenderer($potentialViewRenderer);
				unset($potentialViewRenderer);
				$hasViewRenderer = true;
			}
		}
		if (!$hasViewRenderer) {
			$this->logger->error("ViewRenderer Class not found");
			exit;
		}
	}

	/**
	*	Provides the fully qualified class name of the ViewRenderer that should be used to render the response
	*	App level extenders of CoreLoader can override this method to use their own criteria to select the ViewRenderer
	*	@return string|null $viewRendererName - The fully qualified class name of the ViewRenderer to be used to render the response
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
			$viewRenderOverride = strtoupper($inputData['view_renderer'])."ViewRenderer";
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
	*	@return void
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
				if ($controller instanceof \Core\Interfaces\Controller) {
					$controller->evaluate();
				} else {
					$controller = null;
				}
			}
		}
		if (!$controller) {
			$this->logger->warn("Did not find Controller Class");
			$this->getSite()->setRedirectUrl($config['PATH_HTTP']);
		}
	}

	/**
	*	Asks the ViewRenderer to render the response
	*	@return void
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

