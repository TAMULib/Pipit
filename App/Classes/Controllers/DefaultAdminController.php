<?php
namespace App\Classes\Controllers;
use Core\Classes as Core;

class DefaultAdminController extends Core\AbstractController {
	protected function configure() {
		$this->requireAdmin = true;
	}
	
	protected function loadDefault() {
		$this->setViewName("default");
	}
}