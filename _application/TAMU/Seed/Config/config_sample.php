<?php
define('APP_NAME', 'The Seed App'); 
define('APP_DIRECTORY', 'PHPSeedApp'); 
define('APP_BASE', '_application/');

//server paths
define('PATH_ROOT', '/'); 
define('PATH_FILE', PATH_ROOT.APP_DIRECTORY.'/');
define('PATH_APP', PATH_FILE.APP_BASE);
define('PATH_LIB', PATH_APP."lib/");
define('PATH_CLASSES', PATH_APP."classes/");
define('PATH_INTERFACES', PATH_APP."interfaces/");
define('PATH_CONTROLLERS', PATH_APP."TAMU/Seed/Controllers/");
define('PATH_VIEWS', PATH_APP."views/");

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

// options are mysql and mssql
// mssql has only been tested on Windows 
define('DB_TYPE', 'mysql');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_HOST', '');
define('DB_DATABASE', 'phpseedapp');

//debug mode for PDO database queries
define('DB_DEBUG', true);
?>


