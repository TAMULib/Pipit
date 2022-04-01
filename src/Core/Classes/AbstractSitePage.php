<?php
namespace Core\Classes;
use Core\Interfaces as Interfaces;
/** 
*	An abstract implementation of a SitePage
*	SitePages are used to define metadata like subaction options, titles and user security levels
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

abstract class AbstractSitePage implements Interfaces\SitePage {
	/** @var int $accessLevel The security restriction for the page */
	protected $accessLevel;
	/** @var string $name The name of the page */
	protected $name;
	/** @var string $path The relative directory path to the page */
	protected $path;
	/** @var string $title The UI display title of the page */
	protected $title;
	/** @var string $subTitle The UI display subtitle of the page */
	protected $subTitle;
	/** @var string[] $options The UI displayed subnavigation for the page */
	protected $options = array();
	/** @var boolean $isSearchable Should UIs with search capability show a search box for this page? */
	protected $isSearchable = false;
	/** @var string[] $searchableFields The columns search implementors should use when querying a DataRepository */
	protected $searchableFields = array();

	/**
	*	Default constructor for extenders of AbstractSitePage
	*
	*	@param string $name The name of the page
	*	@param string $path The relative directory path to the page
	*	@param int $accessLevel The security restriction for the page
	*/
	public function __construct($name,$path,$accessLevel) {
		$this->setName($name);
		$this->setPath($path);
		$this->setAccessLevel($accessLevel);
	}

	/**
	*	Gets the access level for the page
	*	@return int The access level for the page
	*/
	public function getAccessLevel() {
		return $this->accessLevel;
	}

	/**
	*	Sets the access level for the page
	*	@param int $accessLevel The access level for the page (Defaults to 0)
	*/
	public function setAccessLevel($accessLevel=0) {
		$this->accessLevel = $accessLevel;
	}

	/**
	*	Gets the name of the page
	*	@return string $name The name of the page
	*/
	public function getName() {
		return $this->name;
	}

	/**
	*	Sets the name of the page
	*	@param string $name The name of the page
	*/
	public function setName($name) {
		$this->name = $name;
	}

	/**
	*	Gets the relative directory path of the page
	*	@return string The relative directory path of the page
	*/
	public function getPath() {
		return $this->path;
	}

	/**
	*	Sets the path of the page
	*	@param string $path The relative directory path of the page
	*/
	public function setPath($path) {
		$this->path = $path;
	}

	abstract public function isAdminPage();
	abstract public function isPublicPage();
	abstract public function setTitle($title);
	abstract public function getTitle();
	abstract public function setSubTitle($subTitle);
	abstract public function getSubTitle();
	abstract public function getOptions();
	abstract public function setOptions($options);
	abstract public function setIsSearchable($isSearchable);
	abstract public function isSearchable();
	abstract public function setSearchableFields($searchableFields);
	abstract public function getSearchableFields();
}

