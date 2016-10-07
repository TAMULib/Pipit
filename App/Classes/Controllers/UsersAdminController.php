<?php
namespace App\Classes\Controllers;
use App\Classes\Data as AppData;
use Core\Classes as Core;

class UsersAdminController extends Core\AbstractController {
	private $usersRepo;

	protected function configure() {
		$this->requireAdmin = true;
		$this->usersRepo = $this->getSite()->getDataRepository("Users");
		$this->getPage()->setTitle("Manage Users");
		$this->getPage()->setOptions(array(
								array("name"=>"list"),
								array("name"=>"add","action"=>"add","modal"=>true)));
		$this->getPage()->setIsSearchable(true);
		$this->getPage()->setSearchableFields(array(array("name"=>"name_last","type"=>"text"),
								array("name"=>"name_first","type"=>"text")));
	}

	protected function search() {
		$data = $this->getSite()->getSanitizedInputData();
		if (isset($data['term'])) {
			$this->getSite()->getViewRenderer()->registerViewVariable("users",$this->usersRepo->search($data['term']));
			$this->setViewName("users.list");
		} elseif (isset($data['advancedsearch'])) {
			$site->getViewRenderer()->registerViewVariable("users",$this->usersRepo->searchAdvanced($data['advancedsearch']));
			$this->setViewName("users.list");
		} else {
			$this->getSite()->addSystemError('There was an error with the search');
		}
	}


	protected function disable() {
		$data = $this->getSite()->getSanitizedInputData();
		if (isset($data['id']) && is_numeric($data['id']) && $this->usersRepo->disableById($data['id'])) {
			$this->getSite()->addSystemMessage('User disabled');
		} else {
			$this->getSite()->addSystemError('Error disabling user');
		}
	}

	protected function enable() {
		$data = $this->getSite()->getSanitizedInputData();
		if (isset($data['id']) && is_numeric($data['id']) && $this->usersRepo->enableById($data['id'])) {
			$this->getSite()->addSystemMessage('User enabled');
		} else {
			$this->getSite()->addSystemError('Error enabling user');
		}
	}

	protected function insert() {
		$data = $this->getSite()->getSanitizedInputData();
		if (isset($data['user']) && $this->usersRepo->add($data['user'])) {
			$this->getSite()->addSystemMessage('User added');
		} else {
			$this->getSite()->addSystemError('Error adding usert');
		}
	}

	protected function add() {
		$this->getPage->setSubTitle('New User');
		$this->setViewName("users.add");
	}

	protected function update() {
		$data = $this->getSite()->getSanitizedInputData();
		if (isset($data['user']) && (isset($data['id']) && is_numeric($data['id'])) && $this->usersRepo->update($data['id'],$data['user'])) {
			$this->getSite()->addSystemMessage('User updated');
		} else {
			$this->getSite()->addSystemError('Error updating user');
		}
	}

	protected function edit() {
		$data = $this->getSite()->getSanitizedInputData();
		$this->getPage()->setSubTitle('Update User');
		if (isset($data['id']) && is_numeric($data['id'])) {
			$this->getSite()->getViewRenderer()->registerViewVariable("user",$this->usersRepo->getById($data['id']));
			$this->setViewName("users.edit");
		}		
	}

	protected function loadDefault() {
		$this->getPage()->setSubTitle('Users');
	 	$this->getSite()->getViewRenderer()->registerViewVariable("users",$this->usersRepo->get());
		$this->setViewName("users.list");
	}


}