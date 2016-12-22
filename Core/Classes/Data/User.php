<?php
namespace Core\Classes\Data;
use Core\Interfaces as Interfaces;

/** 
*	Represents the application user
*	Handles session management, authentication, and authorization
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class User extends DBObject implements Interfaces\User {
	private $sessionName;
	/** @var mixed[] $profile An associative array of the User's profile data */
	private $profile;

	/**
	*	Instantiates a new User
	*/
	public function __construct() {
		parent::__construct();
		$this->sessionUserId = isset($_SESSION[SESSION_SCOPE]['sessionData']['userId']) ? $_SESSION[SESSION_SCOPE]['sessionData']['userId']:NULL;
		$this->primaryTable = 'users';
		if ($this->isLoggedIn()) {
			$this->buildProfile();
		}
	}

	/**
	*	Ends a logged in User's session
	*	@return boolean True on success, false on failure
	*/
	public function logOut() {
		if ($this->isLoggedIn()) {
			unset($_SESSION[SESSION_SCOPE]['sessionData']);
			session_destroy();
			return true;
		}
		return false;
	}

	/**
	*	Checks if the user has a session
	*	@return boolean True if logged in, false if not
	*/
	public function isLoggedIn() {
		if (!empty($this->sessionUserId)) {
			return true;
		}
		return false;
	}

	/**
	*	Hash a plaintext password
	*	@param string $plaintext The plaintext password
	*	@return string The password hash
	*/
	public static function hashPassword($plaintext) {
		return password_hash($plaintext, PASSWORD_DEFAULT);
	}

	/**
	*	Log in a User	
	*	@param string $username The User's username
	*	@param string $password The User's password
	*	@return boolean True on successful login, false on anything else
	*/
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

	/**
	*	Builds the User's profile data which is exposed to the application
	*/
	protected function buildProfile() {
		$sql = "SELECT * FROM {$this->primaryTable} WHERE id=:id";
		if ($user = $this->executeQuery($sql,array(":id"=>$this->sessionUserId))[0]) {
			unset($user['password']);
			foreach ($user as $field=>$value) {
				$this->profile[$field] = $value;
			}
		}
	}

	/**
	*	Retrieves a particular profile value from the User's profile
	*	@param string $field The name of the profile value to retrieve
	*	@return mixed The value of the profile $field, null if the $field is not present on the profile
	*/
	function getProfileValue($field) {
		$temp = $this->getProfile();
		if (array_key_exists($field,$temp)) {
			return $temp[$field];
		}
		return null;
	}

	/**
	*	Returns the User's profile
	*	@return mixed[]
	*/
	function getProfile() {
		return $this->profile;
	}

	/**
	*	Checks if the User is an administrator
	*	@return boolean
	*/
	function isAdmin() {
		if ($this->isLoggedIn() && $this->getProfileValue("isadmin")) {
			return true;
		}
		return false;
	}
}
?>