<?php
namespace Pipit\Classes\Data;
use Pipit\Classes\Exceptions\ConfigurationException;

/** 
*	Represents the application user
*	Handles session management, authentication through CAS, and authorization
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class UserCAS extends UserDB {
	use \Pipit\Traits\FileConfiguration;

	/** @var array<string,array<string,string>> $casPaths A string array representing the CAS configuration */
	private $casPaths;
	/** @var \Pipit\Interfaces\DataRepository $usersRepo A DataRepository representing the app's Users (assumes existence of 'username' and 'iscas' fields) */
	private $usersRepo;

	/**
	*	Instantiates a new UserCAS by negotiating the login process with a configured CAS Server
	*	@param mixed[] $inputData The input data from the request
	*	@param \Pipit\Interfaces\DataRepository $usersRepo A DataRepository representing the app's Users (assumes existence of 'username' and 'iscas' fields)
	*/
	public function __construct($inputData,$usersRepo) {
		$configurationFileName = "user.cas.config";
		$config = null;
		if ($this->configurationFileExists($configurationFileName)) {
			$config = $this->getConfigurationFromFileName($configurationFileName);
		} else {
			throw new ConfigurationException("CAS config file does not exist");
		}

		if (is_array($config)
			&& is_string($config['urls']['login'])
			&& is_string($config['urls']['check'])
			&& is_string($config['urls']['logout'])
		) {
			parent::__construct();
			$appConfig = $this->getAppConfiguration();
			$redirectUrl = is_string($config['url']['redirect']) ? $config['url']['redirect']:$appConfig['PATH_HTTP'];
			$this->casPaths['urls']['login'] = $config['urls']['login'];
			$this->casPaths['urls']['check'] = $config['urls']['check'];
			$this->casPaths['urls']['logout'] = $config['urls']['logout'];
			if (!empty($inputData['ticket'])) {
				$this->usersRepo = $usersRepo;

				if (is_string($inputData['ticket']) && $this->processLogIn($inputData['ticket'])) {
					header("Location:".$redirectUrl);
				}
			} elseif (!$this->isLoggedIn() && !isset($inputData['action'])) {
				$this->initiateLogIn();
			}
		} else {
			throw new ConfigurationException("CAS urls must be configured");
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
			throw new \RuntimeException("Failed to retrieve CAS XML");
		}

		$casXml = simplexml_load_string($file,'SimpleXMLElement::class', 0, 'cas', true);
		if ($casXml) {
			$casXml->registerXPathNamespace("cas", 'http://www.yale.edu/tp/cas');
			$casUserName = $casXml->authenticationSuccess->user;
			$tusers = $this->usersRepo;
			//find an existing, active user or create a new one
			$user = $tusers->searchAdvanced(array("username"=>$casUserName));
			if ($user && is_array($user)) {
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
		}
		return false;
	}

	/**
	*	Redirect to the configured CAS Server's login URL
	*	@return void
	*/
	public function initiatelogIn() {
		header("Location: {$this->casPaths['urls']['login']}");
	}

	public function logIn($username,$password) {
		return false;
	}

	/**
	*	Log the User out from the application, then redirect to the configured CAS Server's logout URL
	*/
	public function logOut() {
		$logOutResult = parent::logOut();
		header("Location: {$this->casPaths['urls']['logout']}");
		return $logOutResult;
	}
}

