<?php
namespace Pipit\Traits;
use Pipit\Lib\CoreFunctions;

trait FileConfiguration {
	/** @var array<string,mixed[]> $configs An array of configuration arrays */
    private $configs = [];

    /**
     * Loads and parses a php ini file into an array keyed by ini sections
     * @param string $configurationFileName The name of the file without extension or path
     * @return void
     */
    private function loadConfigurationFile($configurationFileName) {
        $config = parse_ini_file(CoreFunctions::getInstance()->getAppConfiguration()['PATH_CONFIG'].$configurationFileName.".ini", true);
        if (!$config || !is_array($config)) {
            throw new \RuntimeException("Error parsing configuration file: {$configurationFileName}");
        }

        $replaceables = [];
        foreach ($config as $key=>$value) {
            $replaceable = self::processMatches($key, $value);
            if (count($replaceable) > 0) {
                $replaceables = array_merge($replaceables, $replaceable);
            }
        }

        foreach ($replaceables as $configField=>$templateKeys) {
            $replaceHierarchy = [];
            $replaceKeys = [];
            $replaceValues = [];
            $replaceHierarchy[] = $configField;
            if (is_array($templateKeys)) {
                foreach ($templateKeys as $templateKey) {
                    if (is_array($templateKey)) {
                        foreach ($templateKey as $parentKey=>$children) {
                            $replaceHierarchy[] = $parentKey;
                            self::pairKeyValue($config, $children, $replaceKeys,$replaceValues);
                        }
                    } else {
                        $replaceKeys[] = '{{'.$templateKey.'}}';
                        $replaceValues[] = $config[$templateKey];
                    }
                }
                self::replaceValue($config, $replaceHierarchy, $replaceKeys, $replaceValues);
            }
        }
        $this->configs[$configurationFileName] = $config;
    }

    /**
     * @param mixed[] $config
     * @param array<mixed> $children
     * @param string[] $replaceKeys
     * @param mixed[] $replaceValues
     * @return void
     */
    static private function pairKeyValue($config, $children, &$replaceKeys, &$replaceValues) {
        foreach ($children as $key=>$value) {
            if (!is_array($value)) {
                $replaceKeys[] = '{{'.$value.'}}';
                $replaceValues[] = $config[$value];
            } else {
                self::pairKeyValue($config, $value, $replaceKeys, $replaceValues);
            }
        }
    }

    /**
     * @param array<mixed> $configLevel
     * @param array<string> $hierarchy
     * @param array<string> $replaceKeys
     * @param array<string> $replaceValues
     * @return void
     */
    static private function replaceValue(&$configLevel, $hierarchy, $replaceKeys, $replaceValues) {
        if (count($hierarchy) > 1 && array_key_exists($hierarchy[0], $configLevel)) {
            $nextLevel = &$configLevel[$hierarchy[0]];
            array_shift($hierarchy);
            self::replaceValue($nextLevel,$hierarchy, $replaceKeys, $replaceValues);
        } else {
            if (is_string($configLevel[$hierarchy[0]]) || is_array($configLevel[$hierarchy[0]])) {
                $configLevel[$hierarchy[0]] = str_replace($replaceKeys, $replaceValues, $configLevel[$hierarchy[0]]);
            }
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return array<mixed>
     */
    static private function processMatches($key, $value) {
        $bracketRegex = '/{{(\w*)}}/m';
        $replaceables = [];
        if (is_array($value)) {
            $nestedReplaceables = [];
            foreach ($value as $nestedKey=>$nestedValue) {
                $nestedReplaceables[$nestedKey] = self::processMatches($nestedKey, $nestedValue);
            }
            $replaceables[$key] = $nestedReplaceables;
            return $replaceables;
        }

        $matches = [];
        $matchCount = is_string($value) ? preg_match_all($bracketRegex, $value, $matches, PREG_SET_ORDER, 0):0;
        $finalReplaceables = [];
        if ($matchCount > 0) {
            foreach ($matches as $matchGroup) {
                $replaceables[$key][] = $matchGroup[1];
            }
            $finalReplaceables[$key] = $replaceables[$key];
        }
        return $finalReplaceables;
    }

    /**
     * Retrieves a config array by its corresponding filename
     * @param string $configurationFileName The name of the file without extension or path
     * @return mixed[]
     */
    protected function getConfigurationFromFileName($configurationFileName) {
        if (!array_key_exists($configurationFileName,$this->configs)) {
            $this->loadConfigurationFile($configurationFileName);
        }
        return $this->configs[$configurationFileName];
    }

    /**
     * Checks for the existence of a file with the given filename
     * @param string $configurationFileName The name of the file without extension or path
     * @return boolean
     */
    protected function configurationFileExists($configurationFileName) {
        return is_file(CoreFunctions::getInstance()->getAppConfiguration()['PATH_CONFIG'].$configurationFileName.".ini");
    }
}
