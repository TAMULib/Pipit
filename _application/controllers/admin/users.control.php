<?php
namespace TAMU\Seed;

$page['title'] = 'Manage Users';
$page['navigation'] = array(
						array("name"=>"list"),
						array("name"=>"add","action"=>"add","modal"=>true));
if ($config['ldap']['url'] && $config['ldap']['port']) {
	$page['navigation'][] =	array("name"=>"LDAP Sync","action"=>"ldapsync","modal"=>true);
}
$page['search'] = array(array("name"=>"name_last","type"=>"text"),
						array("name"=>"name_first","type"=>"text"));
$tusers = new Classes\Data\Users();

if (isset($data['action'])) {
	switch ($data['action']) {
		case 'ldapsync':
			$results = $tusers->syncWithLdap();
			foreach ($results as $result) {
				echo "<div>{$result}</div>";
			}
		break;
		case 'search':
			$page['subtitle'] = '<a href="'.$app['path_http'].'">New Search</a> | Results';
			if (isset($data['term'])) {
 				$viewRenderer->registerViewVariable("users",$tusers->searchUsersBasic($data['term']));
				$viewName = "users.list";
			} elseif (isset($data['advancedsearch'])) {
 				$viewRenderer->registerViewVariable("users",$tusers->searchUsersAdvanced($data['advancedsearch']));
				$viewName = "users.list";
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
			$viewName = "users.add";
		break;
		case 'edit':
			$page['subtitle'] = 'Update User';
			if (isset($data['id']) && is_numeric($data['id'])) {
 				$viewRenderer->registerViewVariable("user",$tusers->getUserById($data['id']));
				$viewName = "users.edit";
			}
		break;
	}
} else {
	$page['subtitle'] = 'Users';
 	$viewRenderer->registerViewVariable("users",$tusers->getUsers());
	$viewName = "users.list";
}
$viewRenderer->setPage($page);

?>