<?php
use Pipit\Lib\CoreFunctions;
use Pipit\Classes\Configuration as Configuration;
use Pipit\Classes\Site\CoreSitePage;

define('PATH_CORE', './src/');

define('PATH_CORE_LIB', PATH_CORE.str_replace('\\', '/', "Pipit\\")."Lib/");

define('APP_DIRECTORY', './tests/TestFiles/');

define('PATH_CONFIG', APP_DIRECTORY.'Config/');

define('PATH_HTTP', "testurl");

define("SESSION_SCOPE",APP_DIRECTORY);

define("NAMESPACE_CORE",'Pipit\\');
define("NAMESPACE_APP",'TestFiles\\');

/*
* To enable CAS:
* - Set USECAS to true
* - Configure user.cas.config.ini
* - Define CAS_USER_REPO
*/
define('USECAS', false);
//Required for CAS use: A DataRepository representing the app's Users (requires existence of 'username' and 'iscas' fields)
//define('CAS_USER_REPO','Users');

//Optionally define a custom implementation of \Pipit\Interfaces\User to be used for the GlobalUser
//define('USER_CLASS',NAMESPACE_CORE.'\\Classes\Data\\UserCAS');

define('USESAML', false);

define('SECURITY_PUBLIC',-1);
define('SECURITY_USER',0);
define('SECURITY_ADMIN',1);
define('SECURITY_MANAGER',2);
?>
