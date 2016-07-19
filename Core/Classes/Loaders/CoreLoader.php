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
	private $config;
	private $logger;
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

	public function load() {
		session_start();

		$this->checkRedirect();
		$site = $this->getSiteClass();
		$site = $this->applyViewRenderer($site);

		$this->loadController($site);
		$this->render($site);

	}

	private function checkRedirect() {
		if (!empty($this->getConfig()['forceRedirectUrl'])) {
			header("Location: {$this->getConfig()['forceRedirectUrl']}");
		}
	}

	private function getSiteClass() {
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
		return $site;
	}

	private function applyViewRenderer($site) {
		//set the ViewRenderer
		$config = $this->getConfig();
		$inputData = $site->getSanitizedInputData();
		$viewRendererFlag = false;
		if (!empty($inputData['json'])) {
			$site->setViewRenderer(new CoreClasses\ViewRenderers\JSONViewRenderer());
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
			$site->setViewRenderer(new $className($site->getGlobalUser(),$site->getPages(),$inputData,$config['controllerConfig']['name']));
			$viewRendererFlag = true;
		}
		if (!$viewRendererFlag) {
			$this->logger->error("ViewRenderer Class not found");
			exit;
		}
		return $site;
	}

	private function loadController($site) {
		//try to load the controller
		$config = $this->getConfig();
		$className = $site->getControllerClass($config['controllerConfig']['name']);
		if (class_exists($className)) {
			$controller = new $className($site,$config['controllerConfig']);
			$controller->evaluate();
		} else {
			$this->logger->warn("Did not find Controller Class");
			header("Location:{$config['PATH_HTTP']}");
		}
	}

	private function render($site) {
		//send system messages to the ViewRenderer
		$site->getViewRenderer()->registerAppContextProperty("systemMessages", $site->getSystemMessages());

		//display the content
		$site->getViewRenderer()->renderView();
	}
}
?>