<?php
namespace Core\Interfaces;
/** 
*	An interface defining a Site manager
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface Site {
	public function setViewRenderer($viewRenderer);
	public function getViewRenderer();
	public function getPages();
	public function setPages($pages);
	public function getGlobalUser();
	public function getControllerPath($controllerName);
	public function getSanitizedInputData();
	public function addSystemMessage($systemMessage);
	public function getSystemMessages();
}
?>