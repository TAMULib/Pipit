<?php

//This must be set to the real file path to the base directory of your server, e.g. '/var/www/html/'
define('PATH_ROOT', '/'); 

define('APP_NAME', 'The PipitSeed App'); 
define('APP_DIRECTORY', 'Pipit-seed'); 
define('PATH_CORE', PATH_ROOT.'Pipit/');

define("SESSION_SCOPE",APP_DIRECTORY);

//defines the primary namespaces.
//the autoloader defined in Core/Lib/functions.php depends on these values to find and load Class and Interface files
//Individual files will need to have their namespaces updated to match if these are changed.
define("NAMESPACE_CORE","Pipit\\");

define('SECURITY_PUBLIC',-1);
define('SECURITY_USER',0);
define('SECURITY_ADMIN',1);
?>
