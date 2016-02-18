<?php
spl_autoload_register(function ($name) {
	$filename = "{$GLOBALS['config']['path_classes']}{$name}.class.php";
	if (is_file($filename)) {
		require $filename;
	} else {
		$filename = "{$GLOBALS['config']['path_lib']}{$name}.class.php";
		if (is_file($filename)) {
			require $filename;
		} else {
			$filename = "{$GLOBALS['config']['path_interfaces']}{$name}.php";
			if (is_file($filename)) {
				require $filename;
			}
		}
	}
});

?>