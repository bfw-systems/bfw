<?php

namespace BFW\test\unit\mocks;

/**
 * Mock for Config class
 * To force some configs values
 * 
 * @TODO : Use Override helpers into Config instead of this class.
 */
class ConfigForceDatas extends \BFW\Config
{
    /**
     * Define new config value for a file
     * 
     * @param string $file The filename
     * @param array|\stdClass $newConfig The new config values
     * 
     * @return void
     */
    public function forceConfig($file, $newConfig)
    {
        $this->config[$file] = $newConfig;
    }
    
    /**
     * Change the value of a config key
     * 
     * @param string $file The filename
     * @param string $configKey The config key
     * @param mixed $newValue The new value for the key
     * 
     * @return void
     */
    public function updateKey($file, $configKey, $newValue)
    {
        if (is_array($this->config[$file])) {
            $this->config[$file][$configKey] = $newValue;
        } elseif (is_object($this->config[$file])) {
            $this->config[$file]->$configKey = $newValue;
        }
    }
}
