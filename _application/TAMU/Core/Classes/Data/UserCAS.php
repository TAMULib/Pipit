<?php
namespace TAMU\Core\Classes\Data;
use App\Classes\Data as AppData;

/** 
*	Represents the application user
*	Handles session management, authentication through CAS, and authorization
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class UserCAS extends User {
	private $casPaths;

	public function __construct() {
		parent::__construct();
		$this->casPaths['urls']['login'] = $GLOBALS['config']['CAS_URLS_LOGIN'];
		$this->casPaths['urls']['check'] = $GLOBALS['config']['CAS_URLS_CHECK'];
		$this->casPaths['urls']['logout'] = $GLOBALS['config']['CAS_URLS_LOGOUT'];
	}

	public function processLogIn($ticket) {
		$file = @file($this->casPaths['urls']['check']."&renew=true&ticket={$ticket}");
		if (!$file) {
			die("The authentication process failed to validate through CAS.");
		}
		if (!empty($file[5])) {
			$rawUserName = simplexml_load_string($file[2]);
			//using quotes to force conversion of rawUserName to string
			$casUserName = "{$rawUserName[0]}";
			$tusers = new AppData\Users();

			//find an existing, active user or create a new one
			if (($user = $tusers->searchAdvanced(array("username"=>$casUserName)))) {
				if ($user[0]['inactive'] == 0) {
					$userId = $user[0]['id'];
				}
			} else {
				$userId = $tusers->add(array("username"=>$casUserName,"iscas"=>1));
			}
			if (!empty($userId)) {
				session_regenerate_id(true);
				session_start();
				$_SESSION[SESSION_SCOPE]['sessionData']['userId'] = $userId;
				$this->buildProfile();
				return true;
			}
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