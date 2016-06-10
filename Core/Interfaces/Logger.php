<?php
namespace Core\Interfaces;
/** 
*	An interface defining a Logger
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface Logger {
	public function info($message);
	public function debug($message);
	public function warn($message);
	public function error($message);
}
?>