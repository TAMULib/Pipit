<?php
namespace Core\Classes\Loggers;
/** 
*	A helper class for CoreLogger that defines how a CoreLogger log level corresponds to a PHP error code.
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class LoggerLevel {
	/** @var string $name The name of the logger level */
	private $name;
	/** @var int $phpErrorCode The PHP error code the CoreLogger level maps to */
	private $phpErrorCode;

	/**
	*	Constructs a new LoggerLevel
	*	@param string $name The name of the level
	*	@param int $phpErrorCode The PHP error code the CoreLogger level maps to
	*
	*/
	public function __construct($name,$phpErrorCode) {
		$this->name = $name;
		$this->phpErrorCode = $phpErrorCode;
	}

	/**
	*	Provides the name of the LoggerLevel
	*	@return string The name of the LoggerLevel
	*/
	public function getName() {
		return $this->name;
	}

	/**
	*	Provides the corresponding PHP error code of the LoggerLevel
	*	@return int The corresponding PHP error code of the LoggerLevel
	*/
	public function getPhpErrorCode() {
		return $this->phpErrorCode;
	}
}
