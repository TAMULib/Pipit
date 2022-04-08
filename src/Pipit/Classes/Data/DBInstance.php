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
	/** @var string $type The SQL type */
    private $type = 'mysql';
	/** @var boolean $debug The debug status */
    private $debug = false;

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

                if (array_key_exists('type', $dbConfig)) {
                    $dbType = strtolower($dbConfig['type']);
                    if (in_array($dbType, ['mysql','mssql'])) {
                        $this->type = $dbType;
                    }
                }
                if (array_key_exists('debug', $dbConfig) && $dbConfig['debug']) {
                    $this->debug = true;
                }
            } else {
                throw new \RuntimeException("Problem with database configuration");
            }
        } catch (\RuntimeException $e) {
			CoreFunctions::getInstance()->getLogger()->error("Error processing DBInstance config");
		}
	}

    /**
     * Returns the type of the DBInstance
     * @return string 
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Returns whether or not the DBInstance is in debug mode
     * @return boolean
     */
    public function isDebug() {
        return $this->debug;
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
