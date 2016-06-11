<?php
namespace Core\Lib;
use Core\Classes as CoreClasses;

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

session_start();

//don't recommend using, sanitizing in case someone does
$_SERVER['PHP_SELF'] = htmlentities($_SERVER['PHP_SELF']);

$config = get_defined_constants(true)["user"];

require_once "{$config['PATH_LIB']}functions.php";

//if a logger has been configured, prefer it to the CoreLogger
if (!empty($config['LOGGER_CLASS'])) {
	$logger = new $config['LOGGER_CLASS']();
} else {
	$logger = new CoreClasses\CoreLogger();
}

if (isset($config['LOG_LEVEL'])) {
	$logger->setLogLevel($config['LOG_LEVEL']);
}

if (isset($forceRedirectUrl) && !empty($forceRedirectUrl)) {
	header("Location: {$forceRedirectUrl}");
}

//try to load the App site class
$className = "{$config['NAMESPACE_APP']}Classes\\Site";
if (class_exists($className)) {
	$site = new $className($config,$sitePages);
	$logger->debug("Loaded Site Class: {$className}");
}
$className = null;

if (empty($site)) {
	$logger->error("Site Class not found");
	exit;
}

$site->setLogger($logger);

$pages = $site->getPages();

$data = $site->getSanitizedInputData();

//set the ViewRenderer
if (isset($data['json']) && $data['json']) {
	$site->setViewRenderer(new Classes\ViewRenderers\JSONViewRenderer());
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
	$site->setViewRenderer(new $className($site->getGlobalUser(),$site->getPages(),$data,$controllerName));
	$className = null;
}

//try to load the controller
$className = $site->getControllerClass($controllerName);
if (class_exists($className)) {
	$controller = new $className($site);
	$controller->evaluate();
} else {
	$logger->error("Did not find Controller Class");
	header("Location:{$config['PATH_HTTP']}");
}
$className = null;

//send system messages to the ViewRenderer
$site->getViewRenderer()->registerAppContextProperty("systemMessages", $site->getSystemMessages());

//display the content
$site->getViewRenderer()->renderView();
?>