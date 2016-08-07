<?php

namespace BFW\test\unit\mocks;

class ConfigForceDatas extends \BFW\Config
{
    public function forceConfig($file, $newConfig)
    {
        $this->config[$file] = $newConfig;
    }
}
