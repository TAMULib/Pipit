<?php
namespace Pipit\Interfaces;
use Psr\Log\LoggerInterface;
/** 
*	An interface defining a Logger
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface Logger extends LoggerInterface {
	/**
	* 	@deprecated Use warning(...) instead
	*	Log a message that warns that something may be wrong with the application
	*
	*	@param string $message The warning message
	*	@return void
	*/
	public function warn($message);
}
