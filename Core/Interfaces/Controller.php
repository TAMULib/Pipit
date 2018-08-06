<?php
namespace Core\Interfaces;
/** 
*	An interface defining a Controller
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface Controller {
	/**
	*	The purpose of this method is to match a page request to an internal Controller method responsible for handling that request.
	*	@return void
	*/
	public function evaluate();
}