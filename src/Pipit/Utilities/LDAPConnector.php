<?php
namespace Pipit\Utilities;
use Pipit\Lib\CoreFunctions;
use Pipit\Classes\Exceptions\ConfigurationException;

class LDAPConnector {
    use \Pipit\Traits\FileConfiguration;
    /** @var string $url The url to connect to */
    private $url;
    /** @var int $port The port to connect to */
    private $port;
    /** @var string $user The user to connect with */
    private $user;
    /** @var string $password The password to connect with */
    private $password;
    /** @var \LDAP\Connection|false $handle The LDAP Connection */
    private $handle;

    private const CONFIG_FILE = "ldap.connector";

    /**
    *	Uses either parameters or global config properties to prep for connecting to an LDAP server
    *
    *	@param string|null $url The url to connect to
    *	@param int|null $port The port to connect to
    *	@param string|null $user The user to connect with
    *	@param string|null $password The password to connect with
    *
    */	
    function __construct($url=NULL,$port=NULL,$user=NULL,$password=NULL) {
        if (!self::configIsValid($url, $port, $user, $password)) {
            $configurationFileName = self::CONFIG_FILE;
            $config = null;
            if ($this->configurationFileExists($configurationFileName)) {
                $config = $this->getConfigurationFromFileName($configurationFileName);
            } else {
                throw new ConfigurationException("LDAPConnector config file does not exist");
            }

            if ($config) {
                if (!$url && is_string($config['url'])) {
                    $url = $config['url'];
                }
                if (!$port && is_int($config['port'])) {
                    $port = $config['port'];
                }

                if (!$user && is_string($config['user'])) {
                    $user = $config['user'];
                }

                if (!$password && is_string($config['password'])) {
                    $password = $config['password'];
                }
            }
        }

        if (self::configIsValid($url, $port, $user, $password)) {
            $this->setProperty('url', $url);
            $this->setProperty('port', $port);
            $this->setProperty('user', $user);
            $this->setProperty('password', $password);
        } else {
            throw new ConfigurationException("Problem with LDAP configuration");
        }
    }

    /**
     * Checks the given parameters for validity
     * @param string|null $url An ldap url
     * @param int|null $port The port of the ldap server
     * @param string|null $user The user to connect as
     * @param string|null $password The password to authenticate with
     * @return boolean
     */
    static private function configIsValid($url, $port, $user, $password) {
        return (!$url || ! $port || !$user || !$password);
    }

    /**
     * Returns the value of the property with the given name
     * @return \LDAP\Connection|false
     */
    public function getConnection() {
        if ($this->handle) {
            return $this->handle;
        } else {
            $this->handle = ldap_connect($this->url, $this->port);
            if ($this->handle) {
                //todo: make set_options configurable
                ldap_set_option($this->handle, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($this->handle, LDAP_OPT_REFERRALS, 0);
                if ($this->user && $this->password) {
                    if ($this->bind()) {
                        return $this->handle;
                    }
                } else {
                    return $this->handle;
                }
            }
        }
        return false;
    }

    /**
    * @return boolean
    */
    private function bind() {
        if ($this->handle) {
            return ldap_bind($this->handle, $this->user, $this->password);
        }
        return false;
    }

    /**
    * Assigns a property on the class with the given $name and $value
    * @param string $name The name of the property to create
    * @param mixed $value The value of the property to create
    * @return void
    */
    protected function setProperty($name,$value) {
        $this->$name = $value;
    }

    /**
     * Returns the value of the property with the given name
     * @param string $name The name of the property
     * @return mixed  
     */
    protected function getProperty($name) {
        return $this->$name;
    }
}
