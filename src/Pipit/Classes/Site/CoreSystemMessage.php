<?php
namespace Pipit\Classes\Site;
use Pipit\Interfaces\SystemMessage;

/** 
*	The default implementation of the SystemMessage interface
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class CoreSystemMessage implements SystemMessage {
	/** @var string $messageType Informs the UI about the type of the message (info, warn, success, notify, etc.) */
	private $messageType;
	/** @var string $message The content of the message */
	private $message;

	/**
	*	Constructs a new CoreSystemMessage by setting its type and message
	*	@param string $message The content of the message
	*	@param string $messageType Informs the UI about the type of the message (info, warn, success, notify, etc.)
	*/
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

