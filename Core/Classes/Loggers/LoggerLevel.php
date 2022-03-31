<?php
namespace Core\Classes\Loggers;
use Psr\Log\LogLevel;
/** 
*	A helper class for CoreLogger that defines how a CoreLogger log level corresponds to a PHP error code.
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class LoggerLevel {
	/** @var array<int,int> $errorCodeMap Maps Pipit levels to PHP error codes */
	private static $errorCodeMap = [
		1 => E_USER_NOTICE,
		2 => E_USER_WARNING,
		3 => E_USER_ERROR
	];

	/** @var array<string,int> $logLevelMap Maps PSR LogLevels to Pipit log levels */
	private static $logLevelMap = [
		LogLevel::EMERGENCY => 3,
		LogLevel::ALERT     => 3,
		LogLevel::CRITICAL  => 3,
		LogLevel::ERROR     => 3,
		LogLevel::WARNING   => 2,
		LogLevel::NOTICE    => 1,
		LogLevel::INFO      => 1,
		LogLevel::DEBUG     => 1
	];

	/**
	 * Returns the matching Pipit log level for a given PSR Log level
	 * @param string $psrLevel The PSR level
	 * @return int The Pipit level
	 */
	public static function getInternalLogLevel($psrLevel) {
		return array_key_exists($psrLevel, self::$logLevelMap) ? self::$logLevelMap[$psrLevel]:3;
	}

	/**
	 * Returns the matching PHP error code for a given Pipit log level
	 * @param int $internalLevel The Pipit log level
	 * @return int The PHP error code
	 */
	public static function getPhpErrorCodeByInternalLevel($internalLevel) {
		return array_key_exists($internalLevel, self::$errorCodeMap) ? self::$errorCodeMap[$internalLevel]:E_USER_NOTICE;
	}

	/**
	 * Returns the maximum Pipit log level
	 * @return int The max Pipit level
	 */
	public static function getMaxInternalLevel() {
		$maxLevel = max(self::$logLevelMap);
		return $maxLevel ? $maxLevel:0;
	}
}
