<?php
namespace Core\Classes\Loggers;
use Psr\Log\LogLevel;
/** 
*	A helper class for CoreLogger that defines how a CoreLogger log level corresponds to a PHP error code.
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class LoggerLevel {
	private static $errorCodeMap = [
		LogLevel::EMERGENCY => E_USER_ERROR,
		LogLevel::ALERT     => E_USER_ERROR,
		LogLevel::CRITICAL  => E_USER_ERROR,
		LogLevel::ERROR     => E_USER_ERROR,
		LogLevel::WARNING   => E_USER_WARNING,
		LogLevel::NOTICE    => E_USER_NOTICE,
		LogLevel::INFO      => E_USER_NOTICE,
		LogLevel::DEBUG     => E_USER_NOTICE
	];

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

	public static function getInternalLogLevel($psrLevel) {
		return array_key_exists($psrLevel, self::$logLevelMap) ? self::$logLevelMap[$psrLevel]:3;
	}

	public static function getPhpErrorCodeByInternalLevel($internalLevel) {
		return array_key_exists($internalLevel, self::$errorCodeMap) ? self::$errorCodeMap[$internalLevel]:E_USER_NOTICE;
	}

	public static function getMaxInternalLevel() {
		return max($logLevelMap);
	}
}
