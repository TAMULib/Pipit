<?php
namespace Core\Interfaces;

/** 
*	Loaders are the primary engine of applications
*	They marshall all of the different components of the app stack, resulting in the rendered content seen by the user.
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
interface Loader {
	/**
	*	Start the execution of the application.
	*	@return void	
	*/
	public function load();
}