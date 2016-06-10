<?php
namespace App\Lib;
use App\Classes as AppClasses;

/**
*	App Loader - The entry point for the application. All endpoints lead, here.
*	New functionality can be added before and after the Core Loader include, or the include can be replaced altogether with a complete app level loader implementation.
*	
*	@author Jason Savell <jsavell@library.tamu.edu>
*
*/

include PATH_CORE_LIB."loader.php";
?>