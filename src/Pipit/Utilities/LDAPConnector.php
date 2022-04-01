<?php
namespace Pipit\Utilities;

class LDAPConnector {
	/** @var string $url The url to connect to */
	private $url;
	/** @var int $port The port to connect to */
	private $port;
	/** @var string $user The user to connect with */
	private $user;
	/** @var string $password The password to connect with */
	private $password;
	/** @var resource|false $handle The LDAP Connection */
	private $handle;

	/**
	*	Uses either parameters or global config properties to prep for connecting to an LDAP server
	*
	*	@param string $url The url to connect to
	*	@param int $port The port to connect to
	*	@param string $user The user to connect with
	*	@param string $password The password to connect with
	*
	*/	
	function __construct($url=NULL,$port=NULL,$user=NULL,$password=NULL) {
		$this->url = ($url) ?$url:$GLOBALS['config']['LDAP_URL'];
		$this->port = ($port) ? $port:$GLOBALS['config']['LDAP_PORT'];
		if ($user) {
			$this->setProperty('user',$user);
		} elseif ($GLOBALS['config']['LDAP_USER']) {
			$this->setProperty('user',$GLOBALS['config']['LDAP_USER']);
		}
		if ($password) {
			$this->setProperty('password',$password);
		} elseif ($GLOBALS['config']['LDAP_USER']) {
			$this->setProperty('password',$GLOBALS['config']['LDAP_PASSWORD']);
		}
	}

	/**
	 * Returns the value of the property with the given name
	 * @return resource|false  
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
	* Creates a new property on the class with the given $name and $value
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
