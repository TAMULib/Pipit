<?php
namespace Core\Classes;
use Core\Interfaces as Interfaces;

/** 
*	An abstract implementation of the Logger interface
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class CoreLogger implements Interfaces\Logger {
	private $loggerTypes = array("info","debug","warn","error");

	public function info($message) {
		$this->writeToLog(array($this->loggerTypes[0],$message));
	}
	public function debug($message) {
		$this->writeToLog(array($this->loggerTypes[1],$message));
	}
	public function warn($message) {
		$this->writeToLog(array($this->loggerTypes[2],$message));
	}

	public function error($message) {
		$this->writeToLog(array($this->loggerTypes[3],$message));
	}

	protected function writeToLog($entry) {
		echo '<pre>';
		print_r($entry);
		echo '</pre>';
	}
}
?>