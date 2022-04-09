<?php
namespace Pipit\Classes\Data;
use Pipit\Interfaces as Interfaces;

/** 
*	Represents the application user
*	Handles session management, authentication, and authorization
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class UserDB extends AbstractUser {
	/**
	*	Instantiates a new User
	*/
	public function __construct() {
		$this->primaryTable = 'users';
		parent::__construct();
	}


	/**
	*	Hash a plaintext password
	*	@param string $plaintext The plaintext password
	*	@return string|false The password hash
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
			$row = current($result);
			if (is_array($row) && password_verify($password,$row['password'])) {
				$this->setSessionUserId($row['id']);
				return true;			
			}
		}
		return false;
	}

	/**
	*	Builds the User's profile data which is exposed to the application
	*	@return void
	*/
	protected function buildProfile() {
		$sql = "SELECT * FROM {$this->primaryTable} WHERE id=:id";
		$userResult = $this->executeQuery($sql,array(":id"=>$this->getSessionUserId()));
		if (is_array($userResult) && count($userResult) > 0) {
			$user = current($userResult);
			unset($user['password']);
			foreach ($user as $field=>$value) {
				$this->profile[$field] = $value;
			}
		}
	}
}
