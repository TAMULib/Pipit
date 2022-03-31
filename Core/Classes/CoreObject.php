<?php
namespace Core\Classes;
use Core\Lib as CoreLib;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

/** 
*	A base class for many Core classes to provide shared access to resources and common functions
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class CoreObject {
	/** @var \Core\Interfaces\Logger $logger This provides the configured Logger to all extenders of this class */
	private $logger = null;

	/**
	*	Sets the Logger
	*	@param \Core\Interfaces\Logger $logger An instance of a Logger implementation
	*	@return void
	*/
	private function setLogger(\Core\Interfaces\Logger $logger) {
		$this->logger = $logger;
	}

	/**
	*	Provides a single instance of a configured Logger to all extenders of CoreObject
	*	@return \Core\Interfaces\Logger
	*/
	public function getLogger() {
	    if ($this->logger != null) {
	        return $this->logger;
	    }
		$this->setLogger(CoreLib\getLogger());
		return $this->logger;
	}
}

