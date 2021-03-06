<?php
namespace Core\Classes;
use Core\Interfaces as Interfaces;

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

/** 
*	The default implementation of the Logger interface.
* 	The active logger can be defined in the config file.
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class CoreLogger implements Interfaces\Logger {
	/** @var LoggerLevel[] $loggerTypes The valid log levels */
	private $loggerTypes = array();
	/** @var int $logLevel The log level of a CoreLogger instance (defaults to 3) */
	private $logLevel = 3;

	/**
	*	Instantiates a CoreLogger by defining its valid $loggerTypes
	*/
	public function __construct() {
		array_push($this->loggerTypes,new LoggerLevel("info",E_USER_NOTICE),
									new LoggerLevel("debug",E_USER_NOTICE),
									new LoggerLevel("warn",E_USER_WARNING),
									new LoggerLevel("error",E_USER_ERROR));
	}

	public function info($message) {
		$this->writeToLog(array(0,$message));
	}
	public function debug($message) {
		$this->writeToLog(array(1,$message));
	}
	public function warn($message) {
		$this->writeToLog(array(2,$message));
	}

	public function error($message) {
		$this->writeToLog(array(3,$message));
	}

	/**
	*	Writes an entry to the PHP error log using PHP's trigger_error() function
	*	@param mixed[] $entry An array of log entry data: array(0=>$loggerTypes index,1=>The log message))
	*
	*/
	protected function writeToLog($entry) {
		if ($entry[0] >= $this->logLevel) {
			trigger_error("** ".$this->getFormattedCaller()." ** {$entry[1]} **",$this->loggerTypes[$entry[0]]->getPhpErrorCode());
		}
	}

	/**
	*	Sets the threshold for which log entries should actually get written to the log
	*	@param int $logLevel Corresponds to an index of $loggerTypes
	*/
	public function setLogLevel($logLevel) {
 		if (is_int($logLevel) && $logLevel <= count($this->loggerTypes)) {
			$this->logLevel = $logLevel;
			$this->debug("Log level was set to: {$this->loggerTypes[$logLevel]->getName()}");
		} else {
			$this->warn("Invalid Log Level was requested");
		}
	}

	protected function getCaller() {
		$backTrace = debug_backtrace();
		$caller = array();
		foreach ($backTrace as $trace) {
			if (empty($trace['class']) || ($trace['class'] != get_class($this))) {
				$caller = $trace;
				break;
			}
		}
		return $caller;
	}

	protected function getFormattedCaller() {
		$rawCaller = $this->getCaller();
		return implode(', ',array("line"=>"L{$rawCaller['line']}","file"=>"File: {$rawCaller['file']}","function"=>"Function: {$rawCaller['function']}"));
	}
}
?>
