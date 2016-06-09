<?php
namespace App;
use TAMU\Core as Core;

$page['title'] = 'Manage Users';
$page['navigation'] = array(
						array("name"=>"list"),
						array("name"=>"add","action"=>"add","modal"=>true));
$page['search'] = array(array("name"=>"name_last","type"=>"text"),
						array("name"=>"name_first","type"=>"text"));

$tusers = new Classes\Data\Users();

if (isset($data['action'])) {
	switch ($data['action']) {
		case 'search':
			if (isset($data['term'])) {
 				$site->getViewRenderer()->registerViewVariable("users",$tusers->search($data['term']));
				$viewName = "users.list";
			} elseif (isset($data['advancedsearch'])) {
 				$site->getViewRenderer()->registerViewVariable("users",$tusers->searchAdvanced($data['advancedsearch']));
				$viewName = "users.list";
			} else {
				$site->addSystemError('There was an error with the search');
			}
		break;
		case 'disable':
			if (isset($data['id']) && is_numeric($data['id']) && $tusers->disableById($data['id'])) {
				$site->addSystemMessage('User disabled');
			} else {
				$site->addSystemError('Error disabling user');
			}
		break;
		case 'enable':
			if (isset($data['id']) && is_numeric($data['id']) && $tusers->enableById($data['id'])) {
				$site->addSystemMessage('User enabled');
			} else {
				$site->addSystemError('Error enabling user');
			}
		break;
		case 'insert':
			if (isset($data['user']) && $tusers->add($data['user'])) {
				$site->addSystemMessage('User added');
			} else {
				$site->addSystemError('Error adding usert');
			}
		break;
		case 'update':
			if (isset($data['user']) && (isset($data['id']) && is_numeric($data['id'])) && $tusers->update($data['id'],$data['user'])) {
				$site->addSystemMessage('User updated');
			} else {
				$site->addSystemError('Error updating user');
			}
		break;
		case 'add':
			$page['subtitle'] = 'New User';
			$viewName = "users.add";
		break;
		case 'edit':
			$page['subtitle'] = 'Update User';
			if (isset($data['id']) && is_numeric($data['id'])) {
 				$site->getViewRenderer()->registerViewVariable("user",$tusers->getById($data['id']));
				$viewName = "users.edit";
			}
		break;
	}
} else {
	$page['subtitle'] = 'Users';
 	$site->getViewRenderer()->registerViewVariable("users",$tusers->get());
	$viewName = "users.list";
}

?>