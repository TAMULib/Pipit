<?php
use Core\Lib;
use Core\Classes\Configuration as Configuration;

define('PATH_CORE', './src/');

define('PATH_CORE_LIB', PATH_CORE.str_replace('\\', '/', "Core\\")."Lib/");

define('APP_DIRECTORY', 'testdir');

define('PATH_HTTP', "testurl");

define("SESSION_SCOPE",APP_DIRECTORY);

define("NAMESPACE_CORE",'Core\\');
define("NAMESPACE_APP",'TestFiles\\');
define('DYNAMIC_REPOSITORY_KEY','dynamicRepositories');

define('USECAS', false);

define('SECURITY_PUBLIC',-1);
define('SECURITY_USER',0);
define('SECURITY_ADMIN',1);
define('SECURITY_MANAGER',2);

require_once PATH_CORE_LIB."functions.php";

$GLOBALS['config'] = get_defined_constants(true)["user"];

$GLOBALS['config'][DYNAMIC_REPOSITORY_KEY] = array("Users"=>new Configuration\DynamicDatabaseRepositoryConfiguration('users','id','name_last',null,null));
$GLOBALS['config']['sitePages'] = ["Test" => new \Core\Classes\CoreSitePage("Test","Test",SECURITY_PUBLIC)];
?>
