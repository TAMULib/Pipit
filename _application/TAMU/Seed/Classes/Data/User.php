<?php
namespace TAMU\Seed\Classes\Data;

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

	private function hash($plaintext) {
		return password_hash($plaintext, PASSWORD_DEFAULT);
	}

	public function logIn($username,$password) {
		$sql = "SELECT * FROM {$this->primaryTable} WHERE username=:username";
		if ($result = $this->executeQuery($sql,array(":username"=>$username))) {
			if (password_verify($password,$result[0]['password'])) {
				$this->sessionName = "app".time();
				$_SESSION['sessionName'] = $this->sessionName;
				foreach ($result[0] as $field=>$value) {
					$_SESSION[$this->sessionName]['user'][$field] = $value;
				}
				$this->buildProfile();
				return true;			
			}
		}
		return false;
	}

	protected function buildProfile() {
		foreach ($_SESSION[$this->sessionName]['user'] as $field=>$value) {
			$this->profile[$field] = $value;
		}
		return false;
	}

	function getProfileValue($field) {
		$temp = $this->getProfile();
		if (array_key_exists($field,$temp)) {
			return $temp[$field];
		}
		return false;
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