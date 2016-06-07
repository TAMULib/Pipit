<?php
namespace App\Classes\ViewRenderers;
use TAMU\Core\Classes as CoreClasses;

/** 
*	The default implementation of the ViewRenderer interface.
*	Renders HTML with templated header and footer
*	Would make a good starting point for integration with front end frameworks like Bootstrap
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/


class BootstrapViewRenderer extends CoreClasses\ViewRenderers\HTMLViewRenderer {
	protected $viewPaths = array('bootstrap','html');

	public function __construct(&$globalUser,&$pages,$controllerName) {
		parent::__construct($globalUser,$pages,$controllerName);
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