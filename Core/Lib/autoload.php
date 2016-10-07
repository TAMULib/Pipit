<?php
namespace Core\Lib;
use Core\Classes as CoreClasses;

/*
*   This autoloader will search NAMESPACE_APP for a matching file containing the declaration of that class or interface.
*/
spl_autoload_register(function($class) {
    loadFile($class,NAMESPACE_APP,PATH_APP);
});
echo 'test';
/*
*   This autoloader will search NAMESPACE_CORE for a matching file containing the declaration of that class or interface.
*/

spl_autoload_register(function($class) {
    loadFile($class,NAMESPACE_CORE,PATH_CORE);
});

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

function nameSpaceToPath($nameSpace) {
    return str_replace('\\', '/', $nameSpace);
}