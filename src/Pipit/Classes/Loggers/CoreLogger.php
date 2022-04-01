<?php
namespace Pipit\Classes\Loggers;
use Pipit\Interfaces\Logger;
use Psr\Log\AbstractLogger as PsrAbstractLogger;
use Psr\Log\LogLevel;
/**
*	The default implementation of the Logger interface.
* 	The active logger can be defined in the config file.
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class CoreLogger extends PsrAbstractLogger implements Logger {
	/** @var int $logLevel The log level of a CoreLogger instance (defaults to 3) */
	private $logLevel = 3;

	public function warn($message) {
		$this->writeToLog(LogLevel::WARNING,$message);
	}

	public function log($level, $message, $context=[]) {
		if (is_string($level) && is_string($message)) {
			$messageReplacements = [];
			if (count($context) > 0) {
				foreach ($context as $key=>$value) {
					if (!is_array($value) && (!is_object($value) || method_exists($value, '__toString'))) {
						$messageReplacements['{'.$key.'}'] = $value;
					}
				}
				if (count($messageReplacements) > 0) {
					$message = strtr($message, $messageReplacements);
				}
			}
			$this->writeToLog($level,(string) $message);
		}
	}
	/**
	*	Writes an entry to the PHP error log using PHP's trigger_error() function
	*	@param string $level The log level
	*	@param string $message The log message
	*	@return void
	*/
	protected function writeToLog($level,$message) {
		$internalLevel = LoggerLevel::getInternalLogLevel($level);
		if ($internalLevel >= $this->logLevel) {
			trigger_error("** ".$this->getFormattedCaller()." ** {$message} **", LoggerLevel::getPhpErrorCodeByInternalLevel($internalLevel));
		}
	}

	/**
	*	Sets the threshold for which log entries should actually get written to the log
	*	@param int $logLevel Corresponds to an index of $loggerTypes
	*	@return void
	*/
	public function setLogLevel($logLevel) {
		if (is_int($logLevel) && $logLevel <= LoggerLevel::getMaxInternalLevel()) {
			$this->logLevel = $logLevel;
			$this->debug("Log level was set to: ".$logLevel);
		} else {
			$this->warn("Invalid Log Level was requested");
		}
	}

	/**
	 * Build the debug backtrace and extract the calling class
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
