<?php
namespace Core\Classes;
use Core\Interfaces as Interfaces;

/** 
*	The default implementation of the SystemMessage interface
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class CoreSystemMessage implements Interfaces\SystemMessage {
	private $messageType;
	private $message;

	public function __construct($message,$messageType) {
		$this->setMessage($message);
		$this->setType($messageType);
	}

	public function setType($messageType) {
		$this->messageType = $messageType;
	}

	public function getType() {
		return $this->messageType;
	}

	public function setMessage($message) {
		$this->message = $message;
	}

	public function getMessage() {
		return $this->message;
	}
}
?>