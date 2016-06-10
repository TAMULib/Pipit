<?php
namespace Core\Interfaces;
/** 
*	An interface defining a SystemMessage
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

interface SystemMessage {
	public function setType($messageType);
	public function getType();
	public function setMessage($message);
	public function getMessage();
}
?>