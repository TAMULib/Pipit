<?php
namespace App\Config;
//use App\Classes as AppClasses;
use Core\Classes as CoreClasses;

/*
The $sitePages array represents the app's pages, which are used to generate user facing navigation and load controllers.

The keys correspond to controller names.

Each entry should have a corresponding user reachable file (with an arbitrary relative directory path) that:
	1. Includes the config file
	2. Does one of the following:
		a. Defines a controller and includes the loader 
		b. Redirects with $forceRedirectUrl

It's possible to have user reachable files that aren't represented in this array. They simply won't have a navigation link in the HTML header.

Further configuration of the current SitePage is often done by the controllers that correspond to that SitePage.
*/

$sitePages = array(
			"widgets" => new CoreClasses\CoreSitePage("widgets","widgets",SECURITY_USER),
			"users" => new CoreClasses\CoreSitePage("users","users",SECURITY_ADMIN));

/* If you'd like to use the app level SitePage, use the following $sitePages array, instead, and uncomment the AppClasses namespace alias at the top of this file.
$sitePages = array(
			"widgets" => new AppClasses\SitePage("widgets","widgets",SECURITY_USER),
			"users" => new AppClasses\SitePage("users","users",SECURITY_ADMIN));
*/
?>