<?php
namespace Core\Classes\Data;

/** 
*	Represents the application user
*	Handles session management, authentication through CAS, and authorization
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class UserCAS extends User {
	private $casPaths;
	private $usersRepo;

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

	public function processLogIn($ticket) {
		$file = file_get_contents($this->casPaths['urls']['check']."&renew=true&ticket={$ticket}");
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
				$_SESSION[SESSION_SCOPE]['sessionData']['userId'] = $userId;
				$this->buildProfile();
				return true;
			}
		} else {
			$this->getLogger()->error("Failed to retrieve CAS XML");
		}
		return false;
	}

	public function initiatelogIn() {
		header("Location: {$this->casPaths['urls']['login']}");
	}

	public function logOut() {
		parent::logOut();
		header("Location: {$this->casPaths['urls']['logout']}");
	}
}
?>