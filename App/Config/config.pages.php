<?php
namespace App\Config;
use App\Classes as AppClasses;
//This array represents the app's pages. Used to generate user facing navigation and load controllers
//The keys correspond to controller names
//Each entry should have a corresponding user reachable file (with an arbitrary real directory path) that includes the config file and (defines a controller and includes the loader) or (redirects with $forceRedirectUrl)
//It's possible to have user reachable files that aren't represented in this array. They simply won't have a navigation link in the HTML header.
$sitePages = array(
			"widgets" => new AppClasses\SitePage("widgets","widgets",SECURITY_USER),
			"users" => new AppClasses\SitePage("users","users",SECURITY_ADMIN));
?>