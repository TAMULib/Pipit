<?php
namespace TAMU\Core;
use App\Classes as AppClasses;

/**
*	The entry point for the application. All endpoints lead, here.
*	
*	@author Jason Savell <jsavell@library.tamu.edu>
*
*/


session_start();

require_once "{$config['PATH_LIB']}functions.php";

//This array represents the app's pages. Used to generate user facing navigation and load controllers
//The keys represent controller names
$pages = array(
			"widgets" => array("name"=>"widgets","path"=>"widgets"),
			"users" => array("name"=>"users","path"=>"users","admin"=>true));

$config = get_defined_constants(true)["user"];
//$site = new AppClasses\Site($globaluser,$config,$viewRenderer,$pages);
$site = new AppClasses\Site($config,$pages);


//don't recommend using, sanitizing in case someone does
$_SERVER['PHP_SELF'] = htmlentities($_SERVER['PHP_SELF']);

if (isset($forceRedirectUrl) && !empty($forceRedirectUrl)) {
	header("Location: {$forceRedirectUrl}");
}

$system = array();

//get a sanitized version of GET/POST/REQUEST data
$data = $site->getInputData();

//set the ViewRenderer
if (isset($data['json']) && $data['json']) {
	$site->setViewRenderer(new Classes\ViewRenderers\JSONViewRenderer());
} else {
	$site->setViewRenderer(new Classes\ViewRenderers\HTMLViewRenderer());
}


$controllerPath = $site->getControllerPath($controller);
if (!$controllerPath) {
	header("Location:{$config['PATH_HTTP']}");
}

//try to load the controller
if (!empty($controllerPath) && is_file($controllerPath)) {
	include $controllerPath;
	//if the controller defined a $viewfile, register it with the view renderer
	if (isset($viewName)) {
		if (!empty($pages[$controller]['admin']) && $pages[$controller]['admin'] == true) {
			$site->getViewRenderer()->setView($viewName,$globaluser->isAdmin());
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