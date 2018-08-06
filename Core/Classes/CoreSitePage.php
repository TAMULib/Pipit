<?php
namespace Core\Classes;

/** 
*	The app level implementation of a SitePage
*	SitePages are used to define metadata like subaction options, titles and user security levels
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class CoreSitePage extends AbstractSitePage {

	public function isAdminPage() {
		//any AccessLevel above SECURITY_USER is an admin page
		return ($this->getAccessLevel() > SECURITY_USER);
	}

	public function isPublicPage() {
		return ($this->getAccessLevel() == SECURITY_PUBLIC);
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setSubTitle($subTitle) {
		$this->subTitle = $subTitle;
	}

	public function getSubTitle() {
		return $this->subTitle;
	}

	public function getOptions() {
		return $this->options;
	}

	public function setOptions($options) {
		$this->options = $options;
	}

	public function setIsSearchable($isSearchable) {
		$this->isSearchable = $isSearchable;
	}

	public function isSearchable() {
		return $this->isSearchable;
	} 

	public function setSearchableFields($searchableFields) {
		$this->searchableFields = $searchableFields;
	}

	public function getSearchableFields() {
		return $this->searchableFields;
	}
}