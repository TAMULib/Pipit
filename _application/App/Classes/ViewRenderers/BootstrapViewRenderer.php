<?php
namespace App\Classes\ViewRenderers;
use TAMU\Core\Classes as CoreClasses;

/** 
*	An implementation of the ViewRenderer interface for Bootstrap based theming
*	
*	Looks in the 'bootstrap' Views directory for a given view first. If not found, falls back to the 'html' directory.
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/


class BootstrapViewRenderer extends CoreClasses\ViewRenderers\HTMLViewRenderer {
	protected $viewPaths = array('bootstrap','html');

	public function __construct(&$globalUser,&$pages,&$data,$controllerName) {
		parent::__construct($globalUser,$pages,$data,$controllerName);
		$this->setViewPath($this->viewPaths[0]);
	}

	public function setView($viewFile,$isAdmin=false) {
		$viewSet = false;
		foreach ($this->viewPaths as $viewPath) {
			$this->setViewPath($viewPath);
			if (parent::setView($viewFile,$isAdmin)) {
				$viewSet = true;
				break;
			}
		}
		$this->setViewPath($this->viewPaths[0]);
		if ($viewSet) {
			return true;
		}
		return false;
	}
}
?>