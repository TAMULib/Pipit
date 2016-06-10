<?php
namespace Core\Lib;

/*
*   This autoloader will search both the app and core namespaces, in that order, for the first matching file containing the declaration of that class or interface.
*   In other words, a class/interface at the app level with NAMESPACE\\CLASSNAME will be loaded instead of a core level class/interface with the same NAMESPACE\\CLASSNAME
*
*/

spl_autoload_register(function($class) {
    // project-specific namespace prefixes
    $prefixes = array(NAMESPACE_APP,NAMESPACE_CORE);

    // base directory for the namespace prefix
    $base_dir = PATH_APP;
    // does the class use the namespace prefix?
    foreach ($prefixes as $prefix) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) == 0) {
            // replace the namespace prefix with the base directory, replace namespace
            // separators with directory separators in the relative class name, append
            // with .php
            $file = $base_dir.nameSpaceToPath($class).'.php';
            // if the file exists, require it
            if (file_exists($file)) {
                require $file;
                break;
            }
        }
    }
});

function nameSpaceToPath($nameSpace) {
    return str_replace('\\', '/', $nameSpace);
}

?>