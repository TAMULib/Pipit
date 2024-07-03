<?php
namespace Pipit\Classes\Data;

use Pipit\Classes\Exceptions\ConfigurationException;
use OneLogin\Saml2\Auth;

class UserSAML extends UserDB {
    use \Pipit\Traits\FileConfiguration;

    /** @var \Pipit\Interfaces\DataRepository $usersRepo A DataRepository representing the app's Users (assumes existence of 'username' and 'issaml' fields) */
    private $usersRepo;

    private $settings;

    private const CONFIG_FILE = "user.saml";
    private const DEFAULT_NETID_MAPPING = "netid";

    /**
    *	Instantiates a new UserSAML by negotiating the login process with a configured SAML Server
    *	@param mixed[] $inputData The input data from the request
    *	@param \Pipit\Interfaces\DataRepository $usersRepo A DataRepository representing the app's Users (assumes existence of 'username' and 'issaml' fields)
    */
    public function __construct($inputData, $usersRepo) {

        $this->loadSettings();

        parent::__construct();

        $appConfig = $this->getAppConfiguration();
        $redirectUrl = (array_key_exists('redirect', $this->settings) && is_string($this->settings['redirect'])) ?
                            $this->settings['redirect']:$appConfig['PATH_HTTP'];

        if (!empty($inputData['SAMLResponse'])) {
            $this->usersRepo = $usersRepo;

            if (is_string($inputData['SAMLResponse']) && $this->processLogIn($inputData['SAMLResponse'])) {
                header("Location:".$redirectUrl);
            }
        } elseif (!$this->isLoggedIn() && !isset($inputData['action'])) {
            $this->initiateLogIn();
        }
    }

    protected function checkSettings() {
        return (is_array($this->settings)
                && !empty($this->settings['sp']['entityId'])
                && !empty($this->settings['sp']['assertionConsumerService']['url'])
                && !empty($this->settings['sp']['singleLogoutService']['url'])
                && !empty($this->settings['idp']['entityId'])
                && !empty($this->settings['idp']['singleSignOnService']['url'])
                && !empty($this->settings['idp']['singleLogoutService']['url'])
                && !empty($this->settings['idp']['singleLogoutService']['responseUrl'])
                && !empty($this->settings['idp']['x509cert']));
    }

    protected function loadSettings() {
        $configurationFileName = self::CONFIG_FILE;
        $config = null;
        if ($this->configurationFileExists($configurationFileName)) {
            $config = $this->getConfigurationFromFileName($configurationFileName);
        } else {
            throw new ConfigurationException("SAML config file does not exist");
        }

        $defaultSettingsFile = __DIR__."/../../../../../../onelogin/php-saml/settings_example.php";
        if (is_file($defaultSettingsFile)) {
            require($defaultSettingsFile);
        } else {
            throw new ConfigurationException("Default Settings file is missing. Have you run composer install?");
        }
        $this->settings = array_replace($settings, $config);

        if (!$this->checkSettings()) {
            throw new ConfigurationException("Invalid SAML settings. Please check ".$configurationFileName);
        }
    }

    public function processLogIn() {
        $auth = new Auth($this->settings);

        if (isset($_SESSION) && isset($_SESSION['AuthNRequestID'])) {
            $requestId = $_SESSION['AuthNRequestID'];
        } else {
            $requestId = null;
        }

        $auth->processResponse($requestId);
        unset($_SESSION['AuthNRequestID']);

        $errors = $auth->getErrors();

        if (!empty($errors)) {
            throw new \RuntimeException("SAML error: ".implode(', ',$errors));
        }

        if (!$auth->isAuthenticated()) {
            throw new \RuntimeException("SAML error: Not authenticated");
        }

        $netIdField = array_key_exists('netid', $this->settings['claims']) ? $this->settings['claims']['netid'] : self::DEFAULT_NETID_MAPPING;

        if (!array_key_exists($netIdField, $auth->getAttributes())) {
            throw new \RuntimeException("SAML error: {$netIdField} claim not present in SAML response");
        }

        $samlNetId = $auth->getAttributes()[$netIdField][0];
        return $this->processUser($samlNetId);
    }

    protected function processUser($samlNetId) {
        $tusers = $this->usersRepo;
        //find an existing, active user or create a new one
        $user = $tusers->searchAdvanced(array("username"=>$samlNetId));
        if ($user && is_array($user)) {
            if ($user[0]['inactive'] == 0) {
                $userId = $user[0]['id'];
            }
        } elseif (!empty($samlNetId)) {
            $userId = $tusers->add(array("username"=>$samlNetId,"issaml"=>1));
        }
        if (!empty($userId)) {
            session_regenerate_id(true);
            session_start();
            $this->setSessionUserId($userId);
            $this->buildProfile();
            return true;
        }
        return false;
    }

    public function initiatelogIn() {
        $auth = new Auth($this->settings);
        $auth->login();
    }

    public function logOut() {
        parent::logOut();
        $auth = new Auth($this->settings);
        $auth->logout();
    }

    public function logIn($username,$password) {
        return false;
    }
}
?>
