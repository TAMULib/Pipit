<?php
namespace Pipit\Traits;
use Pipit\Lib\CoreFunctions;
use Pipit\Classes\Exceptions\ConfigurationException;

trait FileConfiguration {
	/** @var array<string,mixed[]> $configs An array of configuration arrays */
    private $configs = [];

    /**
     * Loads and parses a php ini file into an array keyed by ini sections
     * @param string $configurationFileName The name of the file without extension or path
     * @return void
     */
    private function loadConfigurationFile($configurationFileName) {
        $configFilePath = CoreFunctions::getInstance()->getAppConfiguration()['PATH_CONFIG'].$configurationFileName.".ini";
        if ($this->configurationFileExists($configurationFileName)) {
            $config = parse_ini_file($configFilePath, true);
            if (!$config || !is_array($config)) {
                throw new ConfigurationException("Error parsing configuration file: {$configurationFileName}");
            }

            $replaceables = [];
            $configKeys = [];
            //find all template keys in the config array
            array_walk_recursive($config, function($configEntry) use (&$configKeys) { 
                $bracketRegex = '/{{(\w*)}}/m';
                if (is_string($configEntry) && !in_array($configEntry, $configKeys)) {
                    $keyMatches = [];
                    $matchCount = preg_match_all($bracketRegex, $configEntry, $keyMatches, PREG_SET_ORDER, 0);
                    if ($matchCount > 0) {
                        foreach ($keyMatches as $keyMatchGroup) {
                            $configKeys[] = $keyMatchGroup[1];
                        }
                    }
                }
            });
            $configKeys = array_unique($configKeys);
            $configKeyValuePairs = [];

            //pair all template keys with their value from the config array
            array_walk_recursive($config, function($configEntry, $configKey) use ($configKeys, &$configKeyValuePairs) {
                if (in_array($configKey, $configKeys)) {
                    $configKeyValuePairs[$configKey] = $configEntry;
                }
            });

            //apply all template key/values in the config array
            array_walk_recursive($config, function(&$configEntry, $configKey) use ($configKeyValuePairs) {
                if (is_string($configEntry)) {
                    $replaceKeys = [];
                    $replaceValues = [];
                    foreach ($configKeyValuePairs as $key=>$value) {
                        if (stripos($configEntry, '{{'.$key.'}}') !== false) {
                            $replaceKeys[] = '{{'.$key.'}}';
                            $replaceValues[] = $value;
                        }
                    }
                    if (count($replaceKeys) > 0) {
                        $configEntry = str_replace($replaceKeys, $replaceValues, $configEntry);
                    }
                }
            });

            $this->configs[$configurationFileName] = $config;
        } else {
            throw new ConfigurationException("Configuration file not found: {$configFilePath}");
        }
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
