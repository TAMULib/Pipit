<?php
if (isset($forceRedirectUrl) && !empty($forceRedirectUrl)) {
	header("Location: {$forceRedirectUrl}");
}

require_once "{$config['path_lib']}functions.php";
$pages = array(
			"widgets" => array("name"=>"widgets","path"=>"widgets"),
			"users" => array("name"=>"users","path"=>"users","admin"=>true));

if (!isset($data)) {
	$data = $_REQUEST;
}

if (isset($config['usecas']) && $config['usecas']) {
	$globaluser = new usercas($config['path_http']);
	if (!empty($_GET['ticket'])) {
		if ($globaluser->processLogIn($_GET['ticket'])) {
			header("Location:{$config['path_http']}");
		}
	} elseif (!$globaluser->isLoggedIn() && !isset($data['action'])) {
		$globaluser->initiateLogIn();
	}
} else {
	$globaluser = new user();
}

if (isset($data['json']) && $data['json']) {
	$viewRenderer = new jsonviewrenderer();
} else {
	$viewRenderer = new htmlviewrenderer();
}

//load admin controller if user is logged in and an admin page
//if (isset($accesslevel) && ($accesslevel == 1)) {
if (array_key_exists($controller,$pages) || $controller == 'user') {
	if (!empty($pages[$controller]['admin']) && $pages[$controller]['admin'] == true) {
		//if the user is an admin, load the admin controller, otherwise, redirect to the home page
		if ($globaluser->isAdmin()) {
			if ($controller) {
				$viewRenderer->registerAppContextProperty("app_http", "{$config['path_http']}admin/{$controller}/");
				$filename = "{$config['path_controllers']}admin/{$controller}.control.php";
			} else {
				$viewRenderer->registerAppContextProperty("app_http", "{$config['path_http']}admin/");
				$filename = "{$config['path_controllers']}admin/default.control.php";
			}
		} else {
			header("Location:{$config['path_http']}");
		}
	} elseif ($globaluser->isLoggedIn() || (!$globaluser->isLoggedIn() && $controller == 'user')) {
		//load standard controller
		$viewRenderer->registerAppContextProperty("app_http", "{$config['path_http']}{$controller}/");

		$filename = "{$config['path_controllers']}{$controller}.control.php";
	} else {
		header("Location:{$config['path_http']}");
	}
} else {
	$filename = "{$config['path_controllers']}default.control.php";
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


//display the content
$viewRenderer->renderView();
?>