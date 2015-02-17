<?php
function __autoload($name) {
	$filename = "{$GLOBALS['config']['path_classes']}{$name}.class.php";
	if (is_file($filename)) {
		require $filename;
	} else {
		$filename = "{$GLOBALS['config']['path_lib']}{$name}.class.php";
		if (is_file($filename)) {
			require $filename;
		}
	}
}

function loadView($filename,$isadmin=false) {
	$fullpath = "{$GLOBALS['config']['path_views']}".(($isadmin) ? 'admin/':'')."{$filename}";
	if (is_file($fullpath)) {
		return $fullpath;
	}
	return false;
}
?>