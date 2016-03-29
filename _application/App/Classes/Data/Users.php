<?php
namespace App\Classes\Data;
use TAMU\Core\Classes\Data as CoreData;

/** 
*	Repo for managing application Users
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class Users extends CoreData\AbstractDataBaseRepository {
	public function __construct() {
		parent::__construct('users','id','name_last',array('username','email','name_first','name_last','isadmin','inactive'));
	}

	public function searchAdvanced($data) {
		$sql = "SELECT * FROM {$this->primaryTable} u ";
		$conj = "WHERE";
		$bindparams = array();
		foreach ($data as $field=>$value) {
			$sql .= "{$conj} {$field}=:{$field} ";
			$bindparams[":{$field}"] = $value;
			$conj = "AND";
		}
		$sql .= " ORDER BY name_last";
		if ($result = $this->executeQuery($sql,$bindparams)) {
			return $result;
		}
		return false;
	}

	public function search($term) {
		$sql = "SELECT * FROM {$this->primaryTable} WHERE 
				name_last LIKE ? OR 
				name_first LIKE ? OR
				email LIKE ?
				ORDER BY name_last";
		$bindparams = array("%".$term."%","%".$term."%","%".$term."%");
		if ($result = $this->executeQuery($sql,$bindparams)) {
			return $result;
		}
		return false;
	}

	public function disableById($id) {
		return $this->update($id,array('inactive'=>1));
	}

	public function enableById($id) {
		return $this->update($id,array('inactive'=>0));
	}

	public function add($data) {
		$data['password'] = CoreData\User::hashPassword($data['password']);
		$data['inactive'] = 1;
		return parent::add($data);
	}

	public function update($id,$data) {
		if (!empty($data['password'])) {
			$data['password'] = CoreData\User::hashPassword($data['password']);
		}
		return parent::update($id,$data);
	}

	/**
	*	By default, the seed app only allows for disabling users.
	*	See AbstractDataBaseRepository.php for a functional example of removeById
	*/
	public function removeById($id) {
		return null;
	}
}