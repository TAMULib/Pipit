<?php
namespace TAMU\Core;
use App\Classes as AppClasses;

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

$site = new AppClasses\Site($config,$sitePages);

$pages = $site->getPages();

if (isset($forceRedirectUrl) && !empty($forceRedirectUrl)) {
	header("Location: {$forceRedirectUrl}");
}

$system = array();
$data = $site->getSanitizedInputData();

//set the ViewRenderer
if (isset($data['json']) && $data['json']) {
	$site->setViewRenderer(new Classes\ViewRenderers\JSONViewRenderer());
} else {
	$site->setViewRenderer(new Classes\ViewRenderers\HTMLViewRenderer($site->getGlobalUser(),$site->getPages(),$controllerName));
}

$controllerPath = $site->getControllerPath($controllerName);

if (!$controllerPath) {
	header("Location:{$config['PATH_HTTP']}");
}


//try to load the controller
if (!empty($controllerPath) && is_file($controllerPath)) {
	include $controllerPath;
	//if the controller defined a $viewfile, register it with the view renderer
	if (isset($viewName)) {
		if (!empty($pages[$controllerName]['admin']) && $pages[$controllerName]['admin'] == true) {
			$site->getViewRenderer()->setView($viewName,$site->getGlobalUser()->isAdmin());
		} else {
			$site->getViewRenderer()->setView($viewName);
		}
	}
} else {
	$system[] = 'Error loading content';
}

//send system messages to the ViewRenderer
$site->getViewRenderer()->registerAppContextProperty("system", $system);

//display the content
$site->getViewRenderer()->renderView();
?>