<?php
namespace App\Classes;
use Core\Classes as CoreClasses;
/** 
*	The primary site manager
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class Logger extends CoreClasses\CoreLogger {
	public function writeToLog($entry) {
		$entry[1] = "(App Log) {$entry[1]}";
		parent::writeToLog($entry);
	}
}