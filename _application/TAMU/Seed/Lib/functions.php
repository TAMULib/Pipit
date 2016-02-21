<?php

spl_autoload_register(function ($class) {
    // project-specific namespace prefix
    $prefix = 'TAMU\\Seed\\';

    // base directory for the namespace prefix
    $base_dir = PATH_APP;
    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';
    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
?>