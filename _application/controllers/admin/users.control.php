<?php
$page['title'] = 'Manage Users';
$page['navigation'] = array(
						array("name"=>"list"),
						array("name"=>"add","action"=>"add","modal"=>true));
if ($config['ldap']['url'] && $config['ldap']['port']) {
	$page['navigation'][] =	array("name"=>"LDAP Sync","action"=>"ldapsync","modal"=>true);
}
$page['search'] = array(array("name"=>"name_last","type"=>"text"),
						array("name"=>"name_first","type"=>"text"));
$tusers = new users();

if (isset($data['action'])) {
	switch ($data['action']) {
		case 'ldapsync':
			$results = $tusers->syncWithLdap();
			foreach ($results as $result) {
				$out .= "<div>{$result}</div>";
			}
		break;
		case 'search':
			$page['subtitle'] = '<a href="'.$app['path_http'].'">New Search</a> | Results';
			if (isset($data['term'])) {
 				$viewRenderer->registerViewVariable("users",$tusers->searchUsersBasic($data['term']));
				$viewfile = "users.list.view.php";
			} elseif (isset($data['advancedsearch'])) {
 				$viewRenderer->registerViewVariable("users",$tusers->searchUsersAdvanced($data['advancedsearch']));
				$viewfile = "users.list.view.php";
			} else {
				$system[] = 'There was an error with the search';
			}
		break;
		case 'disable':
			if (isset($data['id']) && is_numeric($data['id']) && $tusers->updateUser($data['id'],array('inactive'=>1))) {
				$system[] = 'User disabled';
			} else {
				$system[] = 'Error disabling user';
			}
		break;
		case 'enable':
			if (isset($data['id']) && is_numeric($data['id']) && $tusers->updateUser($data['id'],array('inactive'=>0))) {
				$system[] = 'User enabled';
			} else {
				$system[] = 'Error enabling user';
			}
		break;
		case 'insert':
			if (isset($data['user']) && $tusers->insertUser($data['user'])) {
				$system[] = 'User added';
			} else {
				$system[] = 'Error adding user';
			}
		break;
		case 'update':
			if (isset($data['user']) && (isset($data['id']) && is_numeric($data['id'])) && $tusers->updateUser($data['id'],$data['user'])) {
				$system[] = 'User updated';
			} else {
				$system[] = 'Error updating user';
			}
		break;
		case 'add':
			$page['subtitle'] = 'New User';
			$viewfile = "users.add.view.php";
		break;
		case 'edit':
			$page['subtitle'] = 'Update User';
			if (isset($data['id']) && is_numeric($data['id'])) {
 				$viewRenderer->registerViewVariable("user",$tusers->getUserById($data['id']));
				$viewfile = "users.edit.view.php";
			}
		break;
	}
} else {
	$page['subtitle'] = 'Users';
 	$viewRenderer->registerViewVariable("users",$tusers->getUsers());
	$viewfile = "users.list.view.php";
}
$viewRenderer->setPage($page);

?>