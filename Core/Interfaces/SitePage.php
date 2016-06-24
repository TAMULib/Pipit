<?php
namespace Core\Interfaces;
/** 
*	An interface defining a SitePage
*	SitePages are used to define metadata like subaction options, titles and user security levels
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface SitePage {
	public function getAccessLevel();
	public function setAccessLevel($accessLevel);
	public function getName();
	public function setName();
	public function getPath();
	public function setPath();
	public function setTitle($title);
	public function getTitle();
	public function setSubTitle($subTitle);
	public function getSubTitle();
	public function getOptions();
	public function setOptions($options);
	public function setIsSearchable($isSearchable);
	public function isSearchable();
	public function setSearchableFields($searchableFields);
	public function getSearchableFields();
}
?>