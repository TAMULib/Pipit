<?php
namespace TAMU\Seed;
session_start();

//load the constants
$config = get_defined_constants(true)["user"];

//don't recommend using, sanitizing in case someone does
$_SERVER['PHP_SELF'] = htmlentities($_SERVER['PHP_SELF']);

if (isset($forceRedirectUrl) && !empty($forceRedirectUrl)) {
	header("Location: {$forceRedirectUrl}");
}

$system = array();

require_once "{$config['PATH_LIB']}functions.php";
$pages = array(
			"widgets" => array("name"=>"widgets","path"=>"widgets"),
			"users" => array("name"=>"users","path"=>"users","admin"=>true));

if (!isset($data)) {
	$data = $_REQUEST;
}

if (isset($config['usecas']) && $config['usecas']) {
	$globaluser = new Classes\Data\UserCAS($config['path_http']);
	if (!empty($_GET['ticket'])) {
		if ($globaluser->processLogIn($_GET['ticket'])) {
			header("Location:{$config['PATH_HTTP']}");
		}
	} elseif (!$globaluser->isLoggedIn() && !isset($data['action'])) {
		$globaluser->initiateLogIn();
	}
} else {
	$globaluser = new Classes\Data\User();
}

if (isset($data['json']) && $data['json']) {
	$viewRenderer = new Classes\ViewRenderers\JSONViewRenderer();
} else {
	$viewRenderer = new Classes\ViewRenderers\HTMLViewRenderer();
}

//load admin controller if user is logged in and an admin page
//if (isset($accesslevel) && ($accesslevel == 1)) {
if (array_key_exists($controller,$pages) || $controller == 'user') {
	if (!empty($pages[$controller]['admin']) && $pages[$controller]['admin'] == true) {
		//if the user is an admin, load the admin controller, otherwise, redirect to the home page
		if ($globaluser->isAdmin()) {
			if ($controller) {
				$viewRenderer->registerAppContextProperty("app_http", "{$config['path_http']}admin/{$controller}/");
				$filename = "{$config['PATH_CONTROLLERS']}admin/{$controller}.control.php";
			} else {
				$viewRenderer->registerAppContextProperty("app_http", "{$config['path_http']}admin/");
				$filename = "{$config['PATH_CONTROLLERS']}admin/default.control.php";
			}
		} else {
			header("Location:{$config['PATH_HTTP']}");
		}
	} elseif ($globaluser->isLoggedIn() || (!$globaluser->isLoggedIn() && $controller == 'user')) {
		//load standard controller
		$viewRenderer->registerAppContextProperty("app_http", "{$config['PATH_HTTP']}{$controller}/");

		$filename = "{$config['PATH_CONTROLLERS']}{$controller}.control.php";
	} else {
		header("Location:{$config['PATH_HTTP']}");
	}
} else {
	$filename = "{$config['PATH_CONTROLLERS']}default.control.php";
}

//try to load the controller
if (!empty($filename) && is_file($filename)) {
	include $filename;
	//if the controller defined a $viewfile, register it with the view renderer
	if (isset($viewName)) {
		if (!empty($pages[$controller]['admin']) && $pages[$controller]['admin'] == true) {
			$viewRenderer->setView($viewName,$globaluser->isAdmin());
		} else {
			$viewRenderer->setView($viewName);
		}
	}
} else {
	$system[] = 'Error loading content';
}

$viewRenderer->registerAppContextProperty("system", $system);

//display the content
$viewRenderer->renderView();
?>