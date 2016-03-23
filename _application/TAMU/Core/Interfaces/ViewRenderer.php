<?php
namespace TAMU\Core\Interfaces;
/** 
*	An interface defining a ViewRenderer
*	ViewRenderers are utilized by controllers and handle the presentation of data to the user
*	Built in implementations are HTMLViewRenderer and JSONViewRenderer
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface ViewRenderer {
	public function renderView();
	public function setViewVariables($data);
	public function registerViewVariable($name,$data);
	public function getViewVariables();
	public function getViewVariable($name);
}
?>