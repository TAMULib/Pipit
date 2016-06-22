<?php
namespace Core\Classes;
use Core\Lib as CoreLib;

class CoreObject {
	private $logger = null;

	public function getLogger() {
		return CoreLib\getLogger();
	}
}
?>