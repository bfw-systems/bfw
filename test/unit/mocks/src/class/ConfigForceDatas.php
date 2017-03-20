<?php

namespace BFW\test\unit\mocks;

class ConfigForceDatas extends \BFW\Config
{
    public function forceConfig($file, $newConfig)
    {
        $this->config[$file] = $newConfig;
    }
    
    public function updateKey($file, $configKey, $newValue)
    {
        if (is_array($this->config[$file])) {
            $this->config[$file][$configKey] = $newValue;
        } elseif (is_object($this->config[$file])) {
            $this->config[$file]->$configKey = $newValue;
        }
    }
}
