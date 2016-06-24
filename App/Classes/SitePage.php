<?php
namespace App\Classes;
use Core\Classes as CoreClasses;
/** 
*	The app level implementation of a SitePage
*	SitePages are used to define metadata like subaction options, titles and user security levels
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class SitePage extends CoreClasses\AbstractSitePage {

	public function setTitle($title) {
		$this->setTitle($title);
	}

	public function getTitle() {
		return $this->title;
	}

	public function setSubTitle($subTitle) {
		$this->setSubTitle($subTitle);
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
?>