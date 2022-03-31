<?php
namespace Core\Classes\Loggers;
use Core\Interfaces as Interfaces;
use Psr\Log\AbstractLogger as PsrAbstractLogger;
/** 
*	The default implementation of the Logger interface.
* 	The active logger can be defined in the config file.
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class CoreLogger extends PsrAbstractLogger implements Interfaces\Logger {
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

	public function warn($message) {
		$this->writeToLog(2,$message);
	}

	public function log($level, $message, $context=[]) {
		if (is_int($level) && is_string($message)) {
			$this->writeToLog((int) $level,(string) $message);
		}
	}
	/**
	*	Writes an entry to the PHP error log using PHP's trigger_error() function
	*	@param int $level The log level
	*	@param string $message The log message
	*	@return void
	*/
	protected function writeToLog($level,$message) {
		if ($level >= $this->logLevel) {
			trigger_error("** ".$this->getFormattedCaller()." ** {$level} **",$this->loggerTypes[$message]->getPhpErrorCode());
		}
	}

	/**
	*	Sets the threshold for which log entries should actually get written to the log
	*	@param int $logLevel Corresponds to an index of $loggerTypes
	*	@return void
	*/
	public function setLogLevel($logLevel) {
 		if (is_int($logLevel) && $logLevel <= count($this->loggerTypes)) {
			$this->logLevel = $logLevel;
			$this->debug("Log level was set to: {$this->loggerTypes[$logLevel]->getName()}");
		} else {
			$this->warn("Invalid Log Level was requested");
		}
	}

	/**
	 * Build the debug backtrace and extrac the calling class
	 * @return mixed[] The details of the calling class
	 */
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

	/**
	 * Returns the calling class info as a string
	 * @return string
	 */
	protected function getFormattedCaller() {
		$rawCaller = $this->getCaller();
		return implode(', ',array("line"=>"L".$rawCaller['line'],"file"=>"File: ".$rawCaller['file'],"function"=>"Function: ".$rawCaller['function']));
	}
}

