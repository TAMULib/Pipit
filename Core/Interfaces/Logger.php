<?php
namespace Core\Interfaces;
/** 
*	An interface defining a Logger
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface Logger {
	/**
	*	Log an informational message about the state of the application
	*
	*	@param string $message The info message
	*	@return void
	*/
	public function info($message);

	/**
	*	Log a message that may be helpful when debugging the application
	*
	*	@param string $message The debug message
	*	@return void
	*/
	public function debug($message);

	/**
	*	Log a message that warns that something may be wrong with the application
	*
	*	@param string $message The warning message
	*	@return void
	*/
	public function warn($message);

	/**
	*	Log a message that notifies that the application is/was in an error state.
	*
	*	@param string $message The error message
	*	@return void
	*/
	public function error($message);
}
