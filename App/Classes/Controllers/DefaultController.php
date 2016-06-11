<?php
namespace App\Classes\Controllers;
use Core\Classes as Core;

class DefaultController extends Core\AbstractController {
	protected function loadDefault() {
		$this->setViewName("default");
	}
}