<?php
namespace Pipit\Lib;
use Pipit\Classes\Loggers\CoreLogger;

/**
*	A fallback autoloader to use when not using Composer's autoloaders
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class CoreAutoload {
    /**
     * Require a file with the given parameters
     * @param string $class The class name
     * @param string $nameSpace The namespace of the class
     * @param string $baseDirectory The directory path the class file resides in
     * @return void
     */
    static public function loadFile($class,$nameSpace,$baseDirectory) {
        $len = strlen($nameSpace);
        if (strncmp($nameSpace, $class, $len) == 0) {
            // replace the namespace prefix with the base directory, replace namespace
            // separators with directory separators in the relative class name, append
            // with .php
            $file = $baseDirectory.self::nameSpaceToPath($class).'.php';
            // if the file exists, require it
            if (file_exists($file)) {
                require $file;
            }
        }
    }

    /**
     * Convert a given namespace to its corresponding file path
     * @param string $nameSpace
     * @return string
     */
    static protected function nameSpaceToPath($nameSpace) {
        return str_replace('\\', '/', $nameSpace);
    }
}
