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

        if ($config) {
            foreach ($config as $key=>$value) {
                $replaceables = self::processMatches($key, $value);
            }
            $replaceHierarchy = [];
            $replaceKeys = [];
            $replaceValues = [];
            foreach ($replaceables as $configField=>$templateKeys) {
                $replaceHierarchy[] = $configField;
                foreach ($templateKeys as $templateKey) {
                    if (is_array($templateKey)) {
                        foreach ($templateKey as $parentKey=>$children) {
                            $replaceHierarchy[] = $parentKey;
                            foreach ($children as $childKey=>$childValue) {
                                $replaceKeys[] = '{{'.$childValue.'}}';
                                $replaceValues[] = $config[$childValue];
                            }
                        }
                    } else {
                        $replaceKeys[] = '{{'.$templateKey.'}}';
                        $replaceValues = $config[$configField];
                    }
                }
            }
            echo 'test:';
            print_r($replaceHierarchy);
            print_r($replaceKeys);
            print_r($replaceValues);
            ob_flush();
            self::replaceValue($config, $replaceHierarchy, $replaceKeys, $replaceValues);
            $this->configs[$configurationFileName] = $config;
        }
    }

    static private function replaceValue(&$configLevel, $hierarchy, $replaceKeys, $replaceValues) {
        if (count($hierarchy) > 1) {
            $nextLevel = &$configLevel[$hierarchy[0]];
            array_shift($hierarchy);
            return self::replaceValue($nextLevel,$hierarchy, $replaceKeys, $replaceValues);
        } else {
            $configLevel[$hierarchy[0]] = str_replace($replaceKeys, $replaceValues, $configLevel[$hierarchy[0]]);
        }
    }

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

        $matchCount = preg_match_all($bracketRegex, $value, $matches, PREG_SET_ORDER, 0);
        if ($matchCount && $matchCount > 0) {
            foreach ($matches as $matchGroup) {
                $replaceables[$key][] = $matchGroup[1];
            }
        }
        return $replaceables;
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
