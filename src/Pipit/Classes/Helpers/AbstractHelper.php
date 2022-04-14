<?php
namespace Pipit\Classes\Helpers;
use Pipit\Classes\CoreObject;
use Pipit\Interfaces\Configurable;
use Pipit\Interfaces\Site;

abstract class AbstractHelper extends CoreObject implements Configurable {
	/** @var \Pipit\Interfaces\Site The associated Site implementation */
	private $site;

	/**
	*	Get the site associated with this Helper
	*	@return \Pipit\Interfaces\Site
	*/
	public function getSite() {
		return $this->site;
	}

	/**
	*	Set the site associated with this Helper
	*	@param \Pipit\Interfaces\Site $site An implementation of the \Pipit\Interfaces\Site interface
	*	@return void
	*/
	public function setSite(Site $site) {
		$this->site = $site;
	}

	/**
	*	Override to handle any Helper specific configurations.
	*/
	public function configure(Site $site) {
		$this->setSite($site);
	}
}
