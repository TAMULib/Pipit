<?php
namespace Core\Lib;
use Core\Classes as CoreClasses;
use Core\Interfaces as CoreInterfaces;

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

	public function __construct($controllerName) {
		//don't recommend using, sanitizing in case someone does
		$_SERVER['PHP_SELF'] = htmlentities($_SERVER['PHP_SELF']);

		$GLOBALS['config'] = get_defined_constants(true)["user"];
		$this->controllerName = $controllerName;
	}

	public function load() {
		session_start();

		$config = $GLOBALS['config'];
		require_once PATH_CONFIG.'config.pages.php';

		$logger = getLogger();

		if (!empty($GLOBALS['forceRedirectUrl'])) {
			header("Location: {$GLOBALS['forceRedirectUrl']}");
		}

		if (!empty($config['SITE_CLASS'])) {
			$className = "{$config['NAMESPACE_APP']}Classes\\{$config['SITE_CLASS']}";
			$site = new $className($config,$sitePages);
			$logger->debug("Loaded Configured Class: {$className}");
		} else {
			$site = new CoreClasses\CoreSite($config,$sitePages);
			$logger->debug("Loaded Core Site Class");
		}
		$className = null;

		if (empty($site)) {
			$logger->error("Site Class not found");
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
			$logger->warn("Did not find Controller Class");
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