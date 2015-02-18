<?php

session_start();

//don't recommend using, sanitizing in case someone does
$_SERVER['PHP_SELF'] = htmlentities($_SERVER['PHP_SELF']);

$config['app']['name'] = 'The Seed App';
$config['app']['directory'] = 'PHPSeedApp';

//server paths
$config['path_root'] = "";
$config['path_file'] = "{$config['path_root']}app/";
$config['path_app'] = "{$config['path_file']}_application/";
$config['path_lib'] = "{$config['path_app']}lib/";
$config['path_classes'] = "{$config['path_app']}classes/";
$config['path_controllers'] = "{$config['path_app']}controllers/";
$config['path_views'] = "{$config['path_app']}views/";

//web paths
$config['path_http'] = "http://localhost/app/";
$config['path_css'] = "{$config['path_http']}_application/css/";
$config['path_js'] = "{$config['path_http']}_application/js/";
$config['path_images'] = "{$config['path_http']}_application/images/";
$config['usecas'] = true;

//ldap config
$config['ldap']['url'];
$config['ldap']['port'];
$config['ldap']['user'];
$config['ldap']['password'];

// options are mysql and mssql
// mssql has only been tested on Windows 
$dbconfig['dbtype'] = 'mysql';
$dbconfig['user'] = '';
$dbconfig['password'] = '';
$dbconfig['host'] = '';
$dbconfig['database'] = '';

//debug mode for PDO database queries
$debugDb = true;
?>