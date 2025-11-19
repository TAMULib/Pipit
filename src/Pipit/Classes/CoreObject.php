<?php
namespace Pipit\Classes;
use Pipit\Lib\CoreFunctions;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

/** 
*	A base class for many Core classes to provide shared access to resources and common functions
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class CoreObject {
    /**
    *	Provides a single instance of a configured Logger to all extenders of CoreObject
    *	@return \Pipit\Interfaces\Logger
    */
    public function getLogger() {
        return CoreFunctions::getInstance()->getLogger();
    }

    /**
    *	Provides a single instance of the global app configuration to all extenders of CoreObject
    *	@return mixed[]
    */
    public function getAppConfiguration() {
        return CoreFunctions::getInstance()->getAppConfiguration();
    }

    /**
     *  Test that a given value exists as a key in the given array and is optionally of the given type
     *  @param mixed[] $configArray Some level of the $configuration array
     *  @param mixed $key A key to check for in $configArray
     *  @param string (Optional) One of the defined PHP types used for is_* checking (string, integer, boolean, etc)
     *  @return bool
     */
    public function checkArrayValue($checkArray, $key, $type=null) {
        return CoreFunctions::getInstance()->checkArrayValue($checkArray, $key, $type=null);
    }
}
