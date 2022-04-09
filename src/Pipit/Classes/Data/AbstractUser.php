<?php
namespace Pipit\Classes\Data;
use Pipit\Interfaces as Interfaces;

/** 
*	Represents the application user
*	Handles session management, authentication, and authorization
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
abstract class AbstractUser extends DBObject implements Interfaces\User {
	/** @var string $sessionName A string scoping the user's session variables within their larger PHP $_SESSION array */
	private $sessionName;
	/** @var mixed $sessionUserId A unique identifier for the User to be stored within their session data */
	private $sessionUserId;
	/** @var mixed[] $profile An associative array of the User's profile data */
	protected $profile;

	/**
	*	Instantiates a new User
	*/
	public function __construct() {
        parent::__construct();
        $this->setSessionName(SESSION_SCOPE);
        $this->setSessionUserId();
        if ($this->isLoggedIn()) {
            $this->buildProfile();
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
	*	Get the name of the session, which is used as a key within the $_SESSION array
	*	@return string The name of the session
	*/
	protected function getSessionName() {
		return $this->sessionName;
	}

	/**
	*	Set the name of the session, which is used as a key within the $_SESSION array
	*	@param string $sessionName The name of the session
	*	@return void
	*/
	protected function setSessionName($sessionName) {
		$this->sessionName = $sessionName;
	}

	/**
	*	Get the unique identifier for the User as stored in $_SESSION
	*	@return mixed $userId The User's ID
	*/
	protected function getSessionUserId() {
		return $this->sessionUserId;
	}

	/**
	*	Set the unique identifier for the User as stored in $_SESSION
	*	@param mixed $sessionUserId The User's ID
	*	@return void
	*/
	protected function setSessionUserId($sessionUserId=null) {
		if ($sessionUserId) {
			$_SESSION[$this->getSessionName()]['sessionData']['userId'] = $sessionUserId;
		}
		$this->sessionUserId = isset($_SESSION[$this->getSessionName()]['sessionData']['userId']) ? $_SESSION[$this->getSessionName()]['sessionData']['userId'] : null;
	}

	/**
	*	Log in a User	
	*	@param string $username The User's username
	*	@param string $password The User's password
	*	@return boolean True on successful login, false on anything else
	*/
	abstract public function logIn($username,$password);

	/**
	*	Ends a logged in User's session
	*	@return boolean True on success, false on failure
	*/
	public function logOut() {
		if ($this->isLoggedIn()) {
			unset($_SESSION[$this->sessionName]['sessionData']);
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
		if ($this->isLoggedIn() && $this->getProfileValue("role") > SECURITY_USER) {
			return true;
		}
		return false;
	}

	/**
	*	Builds the User's profile data which is exposed to the application
	*	@return void
	*/
	abstract protected function buildProfile();
}
?>
