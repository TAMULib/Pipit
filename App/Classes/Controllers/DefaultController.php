<?php
namespace App\Classes\Controllers;
use Core\Classes as Core;

class DefaultController extends Core\AbstractController {
	protected function loadDefault() {
		$viewName = (!empty($this->getControllerConfig()['viewName'])) ? $this->getControllerConfig()['viewName']:'default';
		$this->setViewName($viewName);
	}
}