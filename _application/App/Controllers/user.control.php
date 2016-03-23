<?php
namespace App;
use TAMU\Core as Core;

$viewRenderer->registerAppContextProperty("app_http", "{$config['PATH_HTTP']}user.php");

$page['title'] = 'User';

if (!empty($data['action'])) {
	switch ($data['action']) {
		case 'update':
			$tusers = new Classes\Data\Users();
			if (isset($data['user']) && $tusers->update($globaluser->getProfileValue("id"),$data['user'])) {
				$globaluser->refreshProfile();
				$system[] = 'User updated';
			} else {
				$system[] = 'Error updating user';
			}
		break;
		case 'edit':
			$page['subtitle'] = 'Edit Profile';
			$viewRenderer->registerViewVariable("user",$globaluser->getProfile());
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
$viewRenderer->setPage($page);

?>