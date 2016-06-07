<?php
namespace TAMU\Core\Interfaces;
/** 
*	An interface defining a Site manager
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface Site {
	public function __construct(&$siteConfig,&$pages);
	public function setViewRenderer($viewRenderer);
	public function getViewRenderer();
	public function getPages();
	public function getGlobalUser();
	public function getControllerPath($controllerName);
	public function prepInputData();
	public function getInputData();
}
?>