<?php
namespace App\Classes;
use Core\Classes as CoreClasses;
/** 
*	The primary site manager
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class Logger extends CoreClasses\CoreLogger {
	public function writeToLog($entry) {
		echo '<pre>From The App Logger</pre>';
		parent::writeToLog($entry);
	}
}