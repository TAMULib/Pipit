<?php
namespace Pipit\Classes\Data;
use Pipit\Lib\CoreFunctions;
use \PDO;
/**
* 	Provides a PDO DB connection to instances of \Pipit\Classes\Data\DBObject and its descendants
*	@author Jason Savell <jsavell@library.tamu.edu>
*
*/
class DBInstance {
    use \Pipit\Traits\FileConfiguration;

	/** @var \PDO $handle A PDO instance */
	public $handle;
	/** @var \Pipit\Classes\Data\DBInstance $instance An instance of this db class */
	private static $instance = null;

	/**
	*	Instantiates a new \PDO instance using the DBInstance.config config file
	*/
    private function __construct() {
		try {
			$dbConfig = $this->getConfigurationFromFileName("DBInstance.config");
            if (is_string($dbConfig['dsn']) && is_string($dbConfig['user']) && is_string($dbConfig['password'])) {
                $this->handle = new PDO($dbConfig['dsn'], $dbConfig['user'], $dbConfig['password']);
            } else {
                throw new \RuntimeException("Problem with database configuration");
            }
        } catch (\RuntimeException $e) {
			CoreFunctions::getInstance()->getLogger()->debug("DBInstance.config file not found");
		}
	}

	/**
	*	Returns a singleton instance of the db class
	*	@return \Pipit\Classes\Data\DBInstance
	*/
    public static function getInstance() {
        if (self::$instance == null) {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }
}
