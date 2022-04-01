<?php
namespace Core\Interfaces;
/** 
*	An interface defining a Configurable class
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface Configurable {
	/**
	*	Allows instantiators of a Configurable class to trigger instance configuration *after* the __constructor() is callled
	*	@param \Core\Interfaces\Site $site An implementation of the \Core\Interfaces\Site interface
	*	@return void
	*/
	public function configure(Site $site);
}

