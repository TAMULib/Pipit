<?php
use Pipit\Lib\CoreFunctions;
use Pipit\Classes\Configuration as Configuration;
use Pipit\Classes\Site\CoreSitePage;

//$_SERVER['CONTEXT_DOCUMENT_ROOT'];

define('PATH_CORE', './src/');

define('PATH_CORE_LIB', PATH_CORE.str_replace('\\', '/', "Pipit\\")."Lib/");

define('APP_DIRECTORY', './tests/TestFiles/');

define('PATH_CONFIG', APP_DIRECTORY.'Config/');

define('PATH_HTTP', "testurl");

define("SESSION_SCOPE",APP_DIRECTORY);

define("NAMESPACE_CORE",'Pipit\\');
define("NAMESPACE_APP",'TestFiles\\');
define('DYNAMIC_REPOSITORY_KEY','dynamicRepositories');

define('USECAS', false);

define('SECURITY_PUBLIC',-1);
define('SECURITY_USER',0);
define('SECURITY_ADMIN',1);
define('SECURITY_MANAGER',2);

//require_once PATH_CORE_LIB."functions.php";

//$GLOBALS['config'] = get_defined_constants(true)["user"];
$GLOBALS['config'] = CoreFunctions::getInstance()->getAppConfiguration();

$GLOBALS['config'][DYNAMIC_REPOSITORY_KEY] = array("Users"=>new Configuration\DynamicDatabaseRepositoryConfiguration('users','id','name_last',null,null));
?>
