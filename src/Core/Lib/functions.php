<?php
namespace Core\Lib;
use Core\Classes as CoreClasses;

/**
 * Creates a logger property in $GLOBALS from
 * - A client app config for a defined LOGGER_CLASS constant
 * - Falls back to an instance of \Core\Classes\Loggers\CoreLogger 
 * @return \Core\Interfaces\Logger
 */
function getLogger() {
    if (empty($GLOBALS['logger'])) {
        //if a logger has been configured, prefer it to the CoreLogger
        if (!empty($GLOBALS['config']['LOGGER_CLASS'])) {
            $potentialLogger = new $GLOBALS['config']['LOGGER_CLASS']();
            if ($potentialLogger instanceof \Core\Interfaces\Logger) {
                $GLOBALS['logger'] = $potentialLogger;
                unset($potentialLogger);
            }
        }

        if (empty($GLOBALS['logger'])) {
            $GLOBALS['logger'] = new CoreClasses\Loggers\CoreLogger();
        }
        if (isset($GLOBALS['config']['LOG_LEVEL'])) {
            $GLOBALS['logger']->setLogLevel($GLOBALS['config']['LOG_LEVEL']);
        }
    }
    return $GLOBALS['logger'];
}

/**
 * Require a file with the given parameters
 * @param string $class The class name
 * @param string $nameSpace The namespace of the class
 * @param string $baseDirectory The directory path the class file resides in
 * @return void
 */
function loadFile($class,$nameSpace,$baseDirectory) {
    $len = strlen($nameSpace);
    if (strncmp($nameSpace, $class, $len) == 0) {
        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $file = $baseDirectory.nameSpaceToPath($class).'.php';
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
function nameSpaceToPath($nameSpace) {
    return str_replace('\\', '/', $nameSpace);
}

