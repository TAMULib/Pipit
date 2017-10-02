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
	*/
	public function configure(Site $site);
}
?>