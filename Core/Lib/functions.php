<?php
namespace Core\Lib;
use Core\Classes as CoreClasses;

function getLogger() {
    if (empty($GLOBALS['logger'])) {
        //if a logger has been configured, prefer it to the CoreLogger
        if (!empty($GLOBALS['config']['LOGGER_CLASS'])) {
            $GLOBALS['logger'] = new $GLOBALS['config']['LOGGER_CLASS']();
        } else {
            $GLOBALS['logger'] = new CoreClasses\CoreLogger();
        }
        if (isset($GLOBALS['config']['LOG_LEVEL'])) {
            $GLOBALS['logger']->setLogLevel($GLOBALS['config']['LOG_LEVEL']);
        }
    }
    return $GLOBALS['logger'];
}
?>