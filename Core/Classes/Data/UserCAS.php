<?php
namespace Core\Classes\Data;

/** 
*	Represents the application user
*	Handles session management, authentication through CAS, and authorization
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class UserCAS extends UserDB {
	private $casPaths;
	private $usersRepo;

	/**
	*	Instantiates a new UserCAS by negotiating the login process with a configured CAS Server
	*/
	public function __construct($inputData,$usersRepo=null) {
		parent::__construct();
		$this->casPaths['urls']['login'] = $GLOBALS['config']['CAS_URLS_LOGIN'];
		$this->casPaths['urls']['check'] = $GLOBALS['config']['CAS_URLS_CHECK'];
		$this->casPaths['urls']['logout'] = $GLOBALS['config']['CAS_URLS_LOGOUT'];
		if (!empty($inputData['ticket'])) {
			$this->usersRepo = $usersRepo;

			if ($this->processLogIn($inputData['ticket'])) {
				header("Location:{$GLOBALS['config']['CAS_REDIRECT_URL']}");
			}
		} elseif (!$this->isLoggedIn() && !isset($inputData['action'])) {
			$this->initiateLogIn();
		}
	}

	/**
	*	Processes the result of a CAS authentication request, associating an application user with their corresponding CAS record,
	*		and logging them into the application
	*	@param string $ticket The ticket provided by the CAS Server
	*	@return boolean True on successful login, false on anything else
	*/
	public function processLogIn($ticket) {
		$file = file_get_contents($this->casPaths['urls']['check']."&ticket={$ticket}");
		if (!$file) {
			die("The authentication process failed to validate through CAS.");
		}

		if (!empty($file)) {
 			$casXml = simplexml_load_string($file,null, 0, 'cas', true);
			$casXml->registerXPathNamespace("cas", 'http://www.yale.edu/tp/cas');
			$casUserName = $casXml->authenticationSuccess->user;
			$tusers = $this->usersRepo;
			//find an existing, active user or create a new one
			if (($user = $tusers->searchAdvanced(array("username"=>$casUserName)))) {
				if ($user[0]['inactive'] == 0) {
					$userId = $user[0]['id'];
				}
			} elseif (!empty($casUserName)) {
				$userId = $tusers->add(array("username"=>$casUserName,"iscas"=>1));
			}
			if (!empty($userId)) {
				session_regenerate_id(true);
				session_start();
				$this->setSessionUserId($userId);
				$this->buildProfile();
				return true;
			}
		} else {
			$this->getLogger()->error("Failed to retrieve CAS XML");
		}
		return false;
	}

	/**
	*	Redirect to the configured CAS Server's login URL
	*/
	public function initiatelogIn() {
		header("Location: {$this->casPaths['urls']['login']}");
	}

	/**
	*	Log the User out from the application, then redirect to the configured CAS Server's logout URL
	*/
	public function logOut() {
		parent::logOut();
		header("Location: {$this->casPaths['urls']['logout']}");
	}
}
?>
