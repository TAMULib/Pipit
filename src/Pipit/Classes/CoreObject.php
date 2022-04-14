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
}
