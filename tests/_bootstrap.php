<?php
use Core\Lib;

define('PATH_CORE', './');

define('PATH_CORE_LIB', PATH_CORE.str_replace('\\', '/', "Core\\")."Lib/");

define('APP_DIRECTORY', 'testdir');

define('PATH_HTTP', "testurl");

define("SESSION_SCOPE",APP_DIRECTORY);

define("NAMESPACE_APP",'TestFiles\\');
define('sitePages', array());
define('DYNAMIC_REPOSITORY_KEY',null);

define('SECURITY_PUBLIC',-1);
define('SECURITY_USER',0);
define('SECURITY_ADMIN',1);
define('SECURITY_MANAGER',2);

require_once PATH_CORE_LIB."functions.php";

$GLOBALS['config'] = get_defined_constants(true)["user"];
?>
