<?php
namespace Core\Classes;
use Core\Lib as CoreLib;

class CoreObject {
	private $logger = null;

	private function setLogger($logger) {
		$this->logger = $logger;
	}

	public function getLogger() {
	    if ($this->logger) {
	        return $this->logger;
	    }
		$this->setLogger(CoreLib\getLogger());
		return $this->logger;
	}
}
?>