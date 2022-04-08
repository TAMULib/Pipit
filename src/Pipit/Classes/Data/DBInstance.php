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
        $dbConfig = null;
		try {
			$dbConfig = $this->getConfigurationFromFileName("DBInstance.config");
            $checkKeys = ['dsn','host','database','user','password'];
            $validConfig = true;
            foreach ($checkKeys as $key) {
                if (!array_key_exists($key, $dbConfig) || !is_string($dbConfig[$key])) {
                    $validConfig = false;
                    break;
                }
            }
            if ($validConfig) {
                $replaceKeys = [];
                $replaceValues = [];
                //remove dsn from keys
                unset($checkKeys[0]);
                foreach ($checkKeys as $key) {
                    $keyWrap = '{'.$key.'}';
                    if (strripos($dbConfig['dsn'], $keyWrap)) {
                        $replaceKeys[] = $keyWrap;
                        $replaceValues[] = $dbConfig[$key];
                    }
                }
                $dsn = str_replace($replaceKeys, $replaceValues, $dbConfig['dsn']);
                $this->handle = new PDO($dsn, $dbConfig['user'], $dbConfig['password']);
            } else {
                throw new \RuntimeException("Problem with database configuration");
            }
        } catch (\RuntimeException $e) {
			CoreFunctions::getInstance()->getLogger()->error("Error processing DBInstance config");
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
