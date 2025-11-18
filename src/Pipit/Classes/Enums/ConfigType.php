<?php
namespace Pipit\Classes\Enums;
use Pipit\Lib\CoreFunctions;
use Pipit\Interfaces\ConfigTypeMethod;

/**
 * Enumeration representing different configuration variable types.
 *
 * This provides enumeration functions that can test the value and return TRUE/FALSE based on match.
 *
 * The types use incomplete words to avoid potentical conflicts with PHP's reserved types.
 *
 * This also provides "Nul" types that allow for support NULL values to be considered valid.
 */
enum ConfigType implements ConfigTypeMethod {

    /**
     * For default/unassigned/NULL use cases.
     *
     * This formally represents that something is not a configuration type.
     */
    case Non;

    /**
     * The type is NULL.
     */
    case Nul;

    /**
     * The type is anything except for NULL.
     */
    case Any;

    /**
     * The type is an array (is_array()).
     */
    case Arr;

    /**
     * The type is an array (is_array()) or NULL.
     */
    case ArrNul;

    /**
     * The type is a boolean type (is_bool()).
     */
    case Bol;

    /**
     * The type is a boolean type (is_bool()) or NULL.
     */
    case BolNul;

    /**
     * The type is a numeric type (is_numeric()).
     */
    case Num;

    /**
     * The type is a numeric type (is_numeric()) or NULL.
     */
    case NumNul;

    /**
     * The type is an object.
     */
    case Obj;

    /**
     * The type is an object or NULL.
     */
    case ObjNul;

    /**
     * The type is a string.
     */
    case Str;

    /**
     * The type is a string or NULL.
     */
    case StrNul;

    /**
     * @inherit
     */
    public function is($property) {
        $config = $this->getAppConfiguration();

        if ($this->missing($property, $config)) return FALSE;

        if (is_null($config[$property])) {
            return match($this) {
                ConfigType::Nul, ConfigType::ArrNul, ConfigType::BolNul, ConfigType::NumNul, ConfigType::ObjNul, ConfigType::StrNul => TRUE,

                default => FALSE,
            };
        }

        return $this->match($this, $config[$propety]);
    }

    /**
     * @inherit
     */
    public function in($child, $parent) {
        $config = $this->getAppConfiguration();

        if ($this->missing($parent, $config) || !is_array($config[$parent])) return FALSE;
        if ($this->missing($child, $config[$parent])) return FALSE;

        return $this->match($this, $config[$parent][$child]);
    }

    /**
     * Perform the match against some value.
     *
     * @param $enum The configuration type to use.
     * @param $value The value to test.
     *
     * @return bool TRUE on match; FALSE otherwise.
     */
    private function match(ConfigType $enum, $value) {
        return match($enum) {
            ConfigType::Any => TRUE,
            ConfigType::Arr => is_array($value),
            ConfigType::Bol => is_bool($value),
            ConfigType::Num => is_numeric($value),
            ConfigType::Obj => is_object($value),
            ConfigType::Str => is_string($value),

            default => FALSE,
        };
    }

    /**
     * Check if the property is missing or if the configuration is not an array.
     *
     * @param $propety The propety to check.
     * @param $in The variable to check in.
     *
     * @return bool TRUE if either the property is missing or the variable to check in is not an array.
     */
    private function missing($property, $in) {
        return !is_array($in) || !array_key_exists($property, $in) ? TRUE : FALSE;
    }

    /**
     * Provides a single instance of the global app configuration.
     *
     * Enumerations cannot extend or be extended, so this implemented here rather than extending CoreObject.
     *
     * @return mixed[]
     */
    private function getAppConfiguration() {
        return CoreFunctions::getInstance()->getAppConfiguration();
    }

}
