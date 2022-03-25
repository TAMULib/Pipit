<?php
namespace Core\Classes\ViewRenderers;
use Core\Interfaces as Interfaces;

/** 
*	An implementation of the ViewRenderer interface for rendering registered viewvariables as JSON
*
*	@author Jason Savell <jsavell@library.tamu.edu>
*/

class JSONViewRenderer implements Interfaces\ViewRenderer {
	/** @var mixed[] $variables An array of application data to provide to the views */
	private $variables = array();
	/** @var mixed[] $appContext An array of data for the views geared toward the app environment (User session, server paths, config)  */
	private $appContext = null;

	private static $encodeErrors = array(
									JSON_ERROR_NONE => 'No error has occurred',
									JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
									JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
									JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
									JSON_ERROR_SYNTAX => 'Syntax error',
									JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
									JSON_ERROR_RECURSION => 'One or more recursive references in the value to be encoded',
									JSON_ERROR_INF_OR_NAN => 'One or more NAN or INF values in the value to be encoded',
									JSON_ERROR_UNSUPPORTED_TYPE => 'A value of a type that cannot be encoded was given',
									JSON_ERROR_INVALID_PROPERTY_NAME => 'A property name that cannot be encoded was given',
									JSON_ERROR_UTF16 => 'Malformed UTF-16 characters, possibly incorrectly encoded');

	private function getEncodeErrors() {
		return self::$encodeErrors;
	}

	/**
	*	Render as JSON any registered view variables
	*/
	public function renderView() {
		header('Content-Type: application/json;charset=utf-8');
		$json = json_encode($this->getViewVariables());
		if ($json) {
			echo $json;
		} else {
			throw new \RuntimeException($this->getEncodeErrors()[json_last_error()]);
		}
	}

	/*
	*	An implmentation is required by the ViewRenderer interface, but this method is not currently used by JSONViewRenderer
	*/
	public function setView($viewFile,$isAdmin=false) {
	}

	public function setViewVariables($data) {
		$this->variables = $data;
	}

	public function registerViewVariable($name,$data) {
		$this->variables[$name] = $data;
	}

	public function getViewVariables() {
		return $this->variables;
	}

	public function getViewVariable($name) {
		return $this->variables[$name];
	}

	public function registerAppContextProperty($name,$data) {
		$this->appContext[$name] =& $data;
	}

	public function getAppContextProperty($name) {
		return $this->appContext[$name];
	}

	/*
	*	An implmentation is required by the ViewRenderer interface, but this method is not currently used by JSONViewRenderer
	*/
	public function setPage($page) {
	}
}

