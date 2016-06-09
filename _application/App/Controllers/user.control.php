<?php
namespace App;

$site->getViewRenderer()->registerAppContextProperty("app_http", "{$config['PATH_HTTP']}user.php");

$page['title'] = 'User';

if (!empty($data['action'])) {
	switch ($data['action']) {
		case 'update':
			$tusers = new Classes\Data\Users();
			if (isset($data['user']) && $tusers->update($site->getGlobalUser()->getProfileValue("id"),$data['user'])) {
				$site->addSystemMessage('User updated');
			} else {
				$site->addSystemError('Error updating user');
			}
		break;
		case 'edit':
			$page['subtitle'] = 'Edit Profile';
			$site->getViewRenderer()->registerViewVariable("user",$site->getGlobalUser()->getProfile());
			$viewName = 'user.edit';
		break;
		case 'logout':
			if ($site->getGlobalUser()->isLoggedIn()) {
				if ($site->getGlobalUser()->logOut()) {
					$site->addSystemMessage("You've been logged out");

					$viewName = "user.login";
				} else {
					$site->addSystemError('There was an error logging you out');
				}
			} else {
				$site->addSystemError("You don't seem to be logged in");
				$viewName = "user.login";
			}
		break;
		case 'login':
			if ($data['user']['username'] && $data['user']['password']) {
				if ($site->getGlobalUser()->logIn($data['user']['username'],$data['user']['password'])) {
					header("Location:{$config['PATH_HTTP']}");
				} else {
					$site->addSystemError('Invalid username/password combination');
					$viewName = "user.login";
				}
			} else {
				$site->addSystemError('Please provide both your username and password');
			}
		break;
	}
} else {
	if ($site->getGlobalUser()->isLoggedIn()) {
		$viewName = "user.info";
	} else {
		$viewName = "user.login";
	}
}
$site->getViewRenderer()->setPage($page);

?>