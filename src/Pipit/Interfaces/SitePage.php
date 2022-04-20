<?php
namespace Pipit\Interfaces;
/** 
*	An interface defining a SitePage
*	SitePages are used to define metadata like subaction options, titles and user security levels
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface SitePage {
    /**
    *	Get the access level required for viewing the page.
    *	@return mixed
    */
    public function getAccessLevel();

    /**
    *	Set the access level required for viewing the page.
    *	@param mixed $accessLevel
    *	@return void
    */
    public function setAccessLevel($accessLevel);

    /**
    *	Get the short name of the page.
    *	@return string
    */
    public function getName();

    /**
    *	Set the short name of the page.
    *	@param string $name
    *	@return void
    */
    public function setName($name);

    /**
    *	Get the relative path to the page.
    *	@return string $path 
    */
    public function getPath();

    /**
    *	Set the relative path to the page.
    *	@param string $path
    *   @return void
    */
    public function setPath($path);

    /**
    *	Set the user facing title of the page.
    *	@param string $title
    *	@return void
    */
    public function setTitle($title);

    /**
    *	Get the user facing title of the page.
    *	@return string $title
    */
    public function getTitle();

    /**
    *	Set the user facing subtitle of the page.
    *	@param string $subTitle
    *	@return void
    */
    public function setSubTitle($subTitle);

    /**
    *	Get the user facing subtitle of the page.
    *	@return string $subTitle
    */
    public function getSubTitle();

    /**
    *	Get the user facing action options associated with the page.
    *	@return string[] $options
    */
    public function getOptions();

    /**
    *	Set the user facing action options associated with the page.
    *	@param string[] $options
    *	@return void
    */
    public function setOptions($options);

    /**
    *	Enable/Disable user facing searching
    *	@param boolean $isSearchable
    *	@return void
    */
    public function setIsSearchable($isSearchable);

    /**
    *	Does this page have user facing searchable content?
    *	@return boolean $isSearchable
    */
    public function isSearchable();

    /**
    *	Explicitly define the field names that should be searched.
    *	@param string[] $searchableFields
    *	@return void
    */
    public function setSearchableFields($searchableFields);

    /**
    *	Get the field names that should be searched.
    *	@return string[] $searchableFields
    */
    public function getSearchableFields();

    /**
    *	Is this page restricted to administrative users?
    *	@return boolean
    */
    public function isAdminPage();

    /**
    * 	Should this page be available to all users?
    *	@return boolean
    */
    public function isPublicPage();
}

