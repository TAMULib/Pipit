<?php
$configFile = dirname(__FILE__)."/config.json";
if (is_file($configFile)) {
	$config = json_decode(file_get_contents($configFile),true);
	unset($configFile);
} else {
	echo "Couldn't find the config file!";
	die();
}

?>