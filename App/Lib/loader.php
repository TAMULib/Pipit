<?php
namespace App\Lib;
use Core\Classes as CoreClasses;
use Core\Lib as CoreLib;

/**
*	App Loader - The entry point for the application. All endpoints lead, here.
*	Instantiates a Loader implementation and fires off the site load()
*	
*	@author Jason Savell <jsavell@library.tamu.edu>
*
*/

require_once PATH_LIB."functions.php";
require_once PATH_CONFIG.'config.pages.php';

$config = get_defined_constants(true)["user"];
if (!empty($sitePages)) {
	$config['sitePages'] = $sitePages;
	unset($sitePages);
}

if (!empty($forceRedirectUrl)) {
	$config['forceRedirectUrl'] = $forceRedirectUrl;
	unset($forceRedirectUrl);
}

$logger = CoreLib\getLogger();

if (!empty($config['LOADER_CLASS'])) {
	$className = "{$config['NAMESPACE_APP']}Classes\\Loaders\\{$config['LOADER_CLASS']}";
	$siteLoader = new $className($config,$controllerName);
	$logger->debug("Using Configured Loader Class: {$className}");
} else {
	$siteLoader = new CoreClasses\Loaders\CoreLoader($config,$controllerName);
	$logger->debug("Using Default Loader Class: CoreLoader");
}
$siteLoader->load();
?>