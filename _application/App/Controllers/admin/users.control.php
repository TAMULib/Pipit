<?php
namespace App;
use TAMU\Core as Core;

$page['title'] = 'Manage Users';
$page['navigation'] = array(
						array("name"=>"list"),
						array("name"=>"add","action"=>"add","modal"=>true));
if ($config['LDAP_URL'] && $config['LDAP_PORT']) {
	$page['navigation'][] =	array("name"=>"LDAP Sync","action"=>"ldapsync","modal"=>true);
	//todo upgrade to PHP 5.6+ to allow for array constants, moving these configuration to the config file
	$tusers = new Classes\Data\LDAPUsers(array("name_first"=>"givenname","name_last"=>"sn","email"=>"mail","username"=>"samaccountname"),
										array("samaccountname","givenname","sn","mail","edupersonaffiliation"));
} else {
	$tusers = new Classes\Data\Users();
}
$page['search'] = array(array("name"=>"name_last","type"=>"text"),
						array("name"=>"name_first","type"=>"text"));

if (isset($data['action'])) {
	switch ($data['action']) {
		case 'ldapsync':
			$viewRenderer->registerViewVariable("results",$tusers->syncWithLdap());
			$viewName = "users.ldapsync.results";
		break;
		case 'search':
			if (isset($data['term'])) {
 				$viewRenderer->registerViewVariable("users",$tusers->search($data['term']));
				$viewName = "users.list";
			} elseif (isset($data['advancedsearch'])) {
 				$viewRenderer->registerViewVariable("users",$tusers->searchAdvanced($data['advancedsearch']));
				$viewName = "users.list";
			} else {
				$system[] = 'There was an error with the search';
			}
		break;
		case 'disable':
			if (isset($data['id']) && is_numeric($data['id']) && $tusers->disableById($data['id'])) {
				$system[] = 'User disabled';
			} else {
				$system[] = 'Error disabling user';
			}
		break;
		case 'enable':
			if (isset($data['id']) && is_numeric($data['id']) && $tusers->enableById($data['id'])) {
				$system[] = 'User enabled';
			} else {
				$system[] = 'Error enabling user';
			}
		break;
		case 'insert':
			if (isset($data['user']) && $tusers->add($data['user'])) {
				$system[] = 'User added';
			} else {
				$system[] = 'Error adding user';
			}
		break;
		case 'update':
			if (isset($data['user']) && (isset($data['id']) && is_numeric($data['id'])) && $tusers->update($data['id'],$data['user'])) {
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
 				$viewRenderer->registerViewVariable("user",$tusers->getById($data['id']));
				$viewName = "users.edit";
			}
		break;
	}
} else {
	$page['subtitle'] = 'Users';
 	$viewRenderer->registerViewVariable("users",$tusers->get());
	$viewName = "users.list";
}
$viewRenderer->setPage($page);

?>