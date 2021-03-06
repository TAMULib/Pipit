<?php
namespace Core\Classes;
use Core\Lib as CoreLib;

/** 
*	A base class for many Core classes to provide shared access to resources and common functions
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class CoreObject {
	/** @var Interfaces\Logger $logger This provides the configured Logger to all extenders of this class */
	private $logger = null;

	/**
	*	Sets the Logger
	*	@param Core\Interfaces\Logger An instance of a Logger implementation
	*/
	private function setLogger($logger) {
		$this->logger = $logger;
	}

	/**
	*	Provides a single instance of a configured Logger to all extenders of CoreObject
	*	@return Interfaces\Logger 
	*/
	public function getLogger() {
	    if ($this->logger) {
	        return $this->logger;
	    }
		$this->setLogger(CoreLib\getLogger());
		return $this->logger;
	}
}
?>