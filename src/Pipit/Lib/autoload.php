<?php
namespace Pipit\Lib;

/**
*   This autoloader will search NAMESPACE_CORE for a matching file containing the declaration of that class or interface.
*/
spl_autoload_register(function($class) {
    loadFile($class,NAMESPACE_CORE,PATH_CORE);
});
