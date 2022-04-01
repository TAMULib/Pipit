<?php
namespace Pipit\Classes\Configuration;
/** 
*	An abstract implementation of the Configuration class 
*	@author Jason Savell <jsavell@library.tamu.edu>
*/
class AbstractConfiguration {

	/**
	 * Returns an array of all the defined properties for the instance
	 * @return array<string,string>
	 */
	public function getAllProperties() {
		$configProperties = array();
		foreach (get_class_methods($this) as $methodName) {
			$skipMethods = array('__construct','getAllProperties');
			if (!in_array($methodName,$skipMethods)) {
				$propertyName = lcfirst(substr($methodName,3));
				$configProperties[$propertyName] = $this->$methodName();
			}
		}
		return $configProperties;
	}
}