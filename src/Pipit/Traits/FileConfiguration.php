<?php
namespace Pipit\Traits;

trait FileConfiguration {
	/** @var array<string,mixed[]> $configs An array of configuration arrays */
    private $configs = [];

    /**
     * Loads and parses a php ini file into an array keyed by ini sections
     * @param string $configurationFileName The name of the file without extension or path
     * @return void
     */
    private function loadConfigurationFile($configurationFileName) {
        $config = parse_ini_file($GLOBALS['config']['PATH_CONFIG'].$configurationFileName.".ini", true);
        if (!$config || !is_array($config)) {
            throw new \RuntimeException("Error parsing configuration file: {$configurationFileName}");
        }
        $this->configs[$configurationFileName] = $config;
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
        return is_file($GLOBALS['config']['PATH_CONFIG'].$configurationFileName.".ini");
    }
}
