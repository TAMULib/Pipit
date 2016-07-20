<?php
namespace Core\Interfaces;
/** 
*	An interface defining a ViewRenderer
*	ViewRenderers are utilized by controllers and handle the presentation of data to the user
*	Built in implementations are HTMLViewRenderer and JSONViewRenderer
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface ViewRenderer {
	/**
	*	Display the content of the view
	*	@return void
	*/
	public function renderView();

	/**
	*	Set all variables associated with the view at once.
	*	This can also be expected to overwrite previously set view variables
	*	@param mixed[] $data
	*	@return void
	*/
	public function setViewVariables($data);

	/**
	*	Push to the array of registered view variables
	*	@param string $name - The name of the variable
	*	@param mixed $data - The value(s) of the view variable
	*	@return void
	*/
	public function registerViewVariable($name,$data);

	/**
	*	Get the registered view variables
	*	@return mixed[] $viewVariables
	*/
	public function getViewVariables();

	/**
	*	Get a single view variable by its name
	*	@param string $name The name of the variable
	*	@return mixed $viewVariable
	*/
	public function getViewVariable($name);
}
?>