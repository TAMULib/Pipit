<?php
namespace TAMU\Core\Classes\Data;

/** 
*	Represents the application user
*	Handles session management, authentication, and authorization
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class User extends DBObject {
	private $sessionName;
	private $profile;

	public function __construct() {
		parent::__construct();
		$this->sessionName = isset($_SESSION['sessionName']) ? $_SESSION['sessionName']:NULL;
		$this->primaryTable = 'users';
		if ($this->isLoggedIn()) {
			$this->buildProfile();
		}		
	}

	public function logOut() {
		if ($this->isLoggedIn()) {
			unset($_SESSION[$this->sessionName]);
			unset($_SESSION['sessionName']);
			session_destroy();
			return true;
		}
		return false;
	}

	public function isLoggedIn() {
		if (isset($_SESSION[$this->sessionName]['user'])) {
			return true;
		}
		return false;
	}

	public static function hashPassword($plaintext) {
		return password_hash($plaintext, PASSWORD_DEFAULT);
	}

	private function updateSessionData($data) {
		foreach ($data as $field=>$value) {
			if ($field !== 'password') {
				$_SESSION[$this->sessionName]['user'][$field] = $value;
			}
		}
		$this->buildProfile();
	}

	public function logIn($username,$password) {
		$sql = "SELECT * FROM {$this->primaryTable} WHERE username=:username";
		if ($result = $this->executeQuery($sql,array(":username"=>$username))) {
			if (password_verify($password,$result[0]['password'])) {
				$this->sessionName = "app".time();
				$_SESSION['sessionName'] = $this->sessionName;
				$this->updateSessionData($result[0]);
				return true;			
			}
		}
		return false;
	}

	public function refreshProfile() {
		$sql = "SELECT * FROM {$this->primaryTable} WHERE id=:id";
		if ($freshUser = $this->executeQuery($sql,array(":id"=>$this->getProfileValue("id")))[0]) {
			$this->updateSessionData($freshUser);
		}
	}

	protected function buildProfile() {
		foreach ($_SESSION[$this->sessionName]['user'] as $field=>$value) {
			$this->profile[$field] = $value;
		}
	}

	function getProfileValue($field) {
		$temp = $this->getProfile();
		if (array_key_exists($field,$temp)) {
			return $temp[$field];
		}
		return null;
	}

	function getProfile() {
		return $this->profile;
	}

	function isAdmin() {
		if ($this->isLoggedIn() && $this->getProfileValue("isadmin")) {
			return true;
		}
		return false;
	}
}
?>