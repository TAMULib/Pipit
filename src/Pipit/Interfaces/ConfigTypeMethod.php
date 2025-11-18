<?php
namespace Pipit\Interfaces;

/**
 * A method for ConfigType enumerations.
 *
 * This is intended to help enforce a consistent structure on the ConfigType enumerations.
 */
interface ConfigTypeMethod {

    /**
     * Check if the property is of the designated type in the configuration array.
     *
     * @param $property The property to verify the existence and type of.
     *
     * @return bool TRUE if the type check matches all exstence requirements; otherwise FALSE.
     *   If the config is not set or the property does not exist, then this returns FALSE.
     */
    public function is($property);

    /**
     * Check if the child property is of the designated type and is a part of the parent property in the configuration array.
     *
     * @param $child The child property (key name) within the $parent array.
     * @param $parent The parent property that must be an array.
     *
     * @return bool TRUE if the type check matches all exstence requirements for the child within a valid parent array; otherwise FALSE.
     *   If the config is not set or the property does not exist, then this returns FALSE.
     */
    public function in($child, $parent);
}
