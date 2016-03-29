<?php
define('APP_NAME', 'The Seed App'); 
define('APP_DIRECTORY', 'PHPSeedApp'); 
define('APP_BASE', '_application/');

//defines the primary namespaces.
//the autoloader defined in TAMU/Core/Lib/functions.php depends on these values to find and load Class and Interface files
//Individual files will need to have their namespaces updated to match if these are changed.
define("NAMESPACE_CORE","TAMU\\Core\\");
define("NAMESPACE_APP","App\\");

//server paths
define('PATH_ROOT', '/'); 
define('PATH_FILE', PATH_ROOT.APP_DIRECTORY.'/');
define('PATH_APP', PATH_FILE.APP_BASE);
define('PATH_LIB', PATH_APP.str_replace('\\', '/', NAMESPACE_CORE)."Lib/");
define('PATH_CONTROLLERS', PATH_APP.str_replace('\\', '/', NAMESPACE_APP)."Controllers/");
define('PATH_VIEWS', PATH_APP.str_replace('\\', '/', NAMESPACE_APP)."Views/");

//web paths
define('PATH_HTTP', "http://localhost/".APP_DIRECTORY."/");
define('PATH_CSS', PATH_HTTP."resources/css/");
define('PATH_JS', PATH_HTTP."resources/js/");
define('PATH_IMAGES', PATH_HTTP."resources/images/");

define('USECAS', false);

//ldap config
define('LDAP_URL', NULL);
define('LDAP_PORT', NULL);
define('LDAP_USER', NULL);
define('LDAP_PASSWORD', NULL);
define('LDAP_BASE_DN', NULL);
define('LDAP_SEARCH_FILTER', NULL);
//ToDo upgrade to PHP 5.6+ to allow for Array constants
//const LDAP_SEARCH_ATTRIBUTES = array();
//const LDAP_USER_MAP = array();
define('LDAP_INACTIVE_USER_KEY', NULL);
define('LDAP_USERNAME_KEY', NULL);

//db config
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_HOST', '');
define('DB_DATABASE', 'phpseedapp');
define("DB_DSN", 'mysql:host='.DB_HOST.';dbname='.DB_DATABASE);
// mssql has only been tested on Windows 
//define("DB_PDO_DSN", 'sqlsrv:Server='.DB_HOST.';Database='.DB_DATABASE);

//debug mode for PDO database queries
define('DB_DEBUG', false);
?>


