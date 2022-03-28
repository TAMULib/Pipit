<?php
namespace Core\Classes\Helpers;
use Core\Classes as CoreClasses;
use Core\Interfaces as CoreInterfaces;

abstract class AbstractHelper extends CoreClasses\CoreObject implements CoreInterfaces\Configurable {
	/** @var \Core\Interfaces\Site The associated Site implementation */
	private $site;

	/**
	*	Get the site associated with this Helper
	*	@return \Core\Interfaces\Site
	*/
	public function getSite() {
		return $this->site;
	}

	/**
	*	Set the site associated with this Helper
	*	@param \Core\Interfaces\Site $site An implementation of the \Core\Interfaces\Site interface
	*	@return void
	*/
	public function setSite(CoreInterfaces\Site $site) {
		$this->site = $site;
	}

	/**
	*	Override to handle any Helper specific configurations.
	*/
	public function configure(CoreInterfaces\Site $site) {
		$this->setSite($site);
	}
}