<?php
namespace Pipit\Lib;
use Pipit\Classes\Loggers\CoreLogger;

/**
*	A library class that provides shared access to resources and common functions
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class CoreFunctions {
	/** @var \Pipit\Interfaces\Logger $logger This provides the configured Logger to all extenders of this class */
	private $logger = null;

	/** @var mixed[] $appConfiguration This provides the global app configuration to all extenders of this class */
	private $appConfiguration = null;

   	/** @var \Pipit\Lib\CoreFunctions $instance The singleton instance of the CoreFunctions class */
	private static $instance = null;

	/**
	*/
    private function __construct() {
	}

	/**
	*	Sets the Logger
	*	@param \Pipit\Interfaces\Logger $logger An instance of a Logger implementation
	*	@return void
	*/
	private function setLogger(\Pipit\Interfaces\Logger $logger) {
		$this->logger = $logger;
	}

	/**
	*	Provides a single instance of a configured Logger to all extenders of CoreObject
	*	@return \Pipit\Interfaces\Logger
	*/
	public function getLogger() {
	    if ($this->logger == null) {
            $finalLogger = null;
            $config = $this->getAppConfiguration();
            //if a logger has been configured, prefer it to the CoreLogger
            if (!empty($config['LOGGER_CLASS'])) {
                $potentialLogger = new $config['LOGGER_CLASS']();
                if ($potentialLogger instanceof \Pipit\Interfaces\Logger) {
                    $finalLogger = $potentialLogger;
                }
            }

            if (empty($finalLogger)) {
                $finalLogger = new CoreLogger();
            }
            if (isset($config['LOG_LEVEL']) && is_int($config['LOG_LEVEL'])) {
                $finalLogger->setLogLevel($config['LOG_LEVEL']);
            }
    		$this->setLogger($finalLogger);
        }
		return $this->logger;
	}

	/**
	*	Sets the AppConfiguration
	*	@param mixed[] $appConfiguration An array representing the app configuration
	*	@return void
	*/
	private function setAppConfiguration($appConfiguration) {
		$this->appConfiguration = $appConfiguration;
	}

	/**
	*	Provides a single instance of the global app configuration to all extenders of CoreObject
	*	@return mixed[]
	*/
	public function getAppConfiguration() {
	    if ($this->appConfiguration != null) {
	        return $this->appConfiguration;
	    }
		$this->setAppConfiguration(get_defined_constants(true)["user"]);
		return $this->appConfiguration;
	}

	/**
	*	Returns a singleton instance of the CoreFunctions class
	*	@return \Pipit\Lib\CoreFunctions
	*/
    public static function getInstance() {
        if (self::$instance == null) {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }
}
