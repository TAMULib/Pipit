<?php
namespace Core\Classes\Loaders;
use Core\Classes as CoreClasses;
use Core\Interfaces as CoreInterfaces;
use Core\Lib as CoreLib;

/**
*	The Core Loader is the default entry point for the application. All endpoints lead, here, by way of the App Loader.
*
*	The Core Loader is responsible for:
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
	private $controllerName;
	private $config;
	private $logger;

	public function __construct($config,$controllerName) {
		$this->config = $config;
		$this->controllerName = $controllerName;
		$this->logger = CoreLib\getLogger();
	}

	protected function setConfig($config) {
		$this->config = $config;
	}

	protected function getConfig() {
		return $this->config;
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
		return $site;
	}

	public function load() {
		session_start();

		$config = $this->getConfig();

		$this->checkRedirect();

		$site = $this->getSiteClass();

		if (empty($site)) {
			$this->logger->error("Site Class not found");
			exit;
		}

		$data = $site->getSanitizedInputData();

		//set the ViewRenderer
		if (!empty($data['json'])) {
			$site->setViewRenderer(new CoreClasses\ViewRenderers\JSONViewRenderer());
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
			$site->setViewRenderer(new $className($site->getGlobalUser(),$site->getPages(),$data,$this->controllerName));
			$className = null;
		}

		//try to load the controller
		$className = $site->getControllerClass($this->controllerName);
		if (class_exists($className)) {
			$controllerConfig = (!empty($controllerConfig)) ? $controllerConfig:null;
			$controller = new $className($site,$controllerConfig);
			$controller->evaluate();
		} else {
			$this->logger->warn("Did not find Controller Class");
			header("Location:{$config['PATH_HTTP']}");
		}
		$className = null;

		//send system messages to the ViewRenderer
		$site->getViewRenderer()->registerAppContextProperty("systemMessages", $site->getSystemMessages());

		//display the content
		$site->getViewRenderer()->renderView();
	}
}
?>