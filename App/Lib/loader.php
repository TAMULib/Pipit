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

$siteLoader = new CoreLib\CoreLoader($controllerName);
$siteLoader->load();
?>