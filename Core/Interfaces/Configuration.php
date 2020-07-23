<?php
namespace Core\Interfaces;
/** 
*	An interface defining a Configuration
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface Configuration {
	/**
	*	Provides an associative array of all the properties defined by the Configuration implementation instance
	*/
	public function getAllProperties();
}
