<?php
namespace Core\Classes\Data;

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
		$this->sessionUserId = isset($_SESSION[SESSION_SCOPE]['sessionData']['userId']) ? $_SESSION[SESSION_SCOPE]['sessionData']['userId']:NULL;
		$this->primaryTable = 'users';
		if ($this->isLoggedIn()) {
			$this->buildProfile();
		}
	}

	public function logOut() {
		if ($this->isLoggedIn()) {
			unset($_SESSION[SESSION_SCOPE]['sessionData']);
			session_destroy();
			return true;
		}
		return false;
	}

	public function isLoggedIn() {
		if (!empty($this->sessionUserId)) {
			return true;
		}
		return false;
	}

	public static function hashPassword($plaintext) {
		return password_hash($plaintext, PASSWORD_DEFAULT);
	}

	public function logIn($username,$password) {
		session_regenerate_id(true);
		$sql = "SELECT id,password FROM {$this->primaryTable} WHERE username=:username AND inactive=0";
		if ($result = $this->executeQuery($sql,array(":username"=>$username))) {
			if (password_verify($password,$result[0]['password'])) {
				$_SESSION[SESSION_SCOPE]['sessionData']['userId'] = $result[0]['id'];
				return true;			
			}
		}
		return false;
	}

	protected function buildProfile() {
		$sql = "SELECT * FROM {$this->primaryTable} WHERE id=:id";
		if ($user = $this->executeQuery($sql,array(":id"=>$this->sessionUserId))[0]) {
			unset($user['password']);
			foreach ($user as $field=>$value) {
				$this->profile[$field] = $value;
			}
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