<?php
spl_autoload_register(function ($name) {
	$classLocations = array(
							array("path"=>"{$GLOBALS['config']['path_classes']}data/","suffix"=>"class"),
							array("path"=>"{$GLOBALS['config']['path_classes']}viewrenderers/","suffix"=>"class"),
							array("path"=>"{$GLOBALS['config']['path_interfaces']}","suffix"=>"interface"),
							array("path"=>"{$GLOBALS['config']['path_lib']}","suffix"=>"class"));
	$fileName = null;
	foreach ($classLocations as $location) {
		$fileName = "{$location['path']}{$name}.{$location['suffix']}.php";
		if (is_file($fileName)) {
			break;
		}
	}
	if ($fileName) {
		require $fileName;
	}
});

?>