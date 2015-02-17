<?php
class ldap {
	private $url;
	private $port;

	private $user;
	private $password;

	private $handle;

	function __construct($url,$port,$user=NULL,$password=NULL) {
		$this->port = $port;
		$this->url = $url;
		if ($user) {
			$this->setProperty('user',$user);
		}
		if ($password) {
			$this->setProperty('password',$password);
		}
	}

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

	private function bind() {
		return ldap_bind($this->handle, $this->user, $this->password);
	}

	protected function setProperty($name,$value) {
		$this->$name = $value;
	}

	protected function getProperty($name) {
		return $this->$name;
	}
}