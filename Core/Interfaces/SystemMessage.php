<?php
namespace Core\Interfaces;

/**
*   SystemMessage is used to provide UI updates to users.
*
*   @author Jason Savell <jsavell@library.tamu.edu>
*/
interface SystemMessage {
    /**
    *   Set the type of the message
    *   @param mixed $messageType
    */
    public function setType($messageType);

    /**
    *   Get the type of the message
    *   @return mixed $messageType
    */
    public function getType();

    /**
    *   Set the content of the message
    *   @param string $message
    */
    public function setMessage($message);

    /**
    *   Get the content of the message
    *   @return string $message
    */
    public function getMessage();
}
?>
