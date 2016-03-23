<?php
namespace App;
use TAMU\Core as Core;

$viewRenderer->registerAppContextProperty("app_http", "{$config['PATH_HTTP']}user.php");

if (!empty($data['action'])) {
	switch ($data['action']) {
		case 'edit':
			$viewName = 'user.edit';
		break;
		case 'logout':
			if ($globaluser->isLoggedIn()) {
				if ($globaluser->logOut()) {
					$system[] = "You've been logged out";
					$viewName = "user.login";
				} else {
					$system[] = 'There was an error logging you out';
				}
			} else {
				$system[] = "You don't seem to be logged in";
				$viewName = "user.login";
			}
		break;
		case 'login':
			if ($data['user']['username'] && $data['user']['password']) {
				if ($globaluser->logIn($data['user']['username'],$data['user']['password'])) {
					header("Location:{$config['path_http']}");
				} else {
					$system[] = 'Invalid username/password combination';
					$viewName = "user.login";
				}
			} else {
				$system[] = 'Please provide both your username and password';
			}
		break;
	}
} else {
	if ($globaluser->isLoggedIn()) {
		$viewName = "user.info";
	} else {
		$viewName = "user.login";
	}
}
?>