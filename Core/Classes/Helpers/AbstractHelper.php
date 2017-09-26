<?php
namespace Core\Classes\Helpers;
use Core\Classes as CoreClasses;

abstract class AbstractHelper extends CoreClasses\CoreObject {
	private $site;

	public function getSite() {
		return $this->site;
	}

	public function setSite($site) {
		$this->site = $site;
	}
}