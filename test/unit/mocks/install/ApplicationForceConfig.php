<?php

namespace BFW\Install\test\unit\mocks;

class ApplicationForceConfig extends \BFW\Install\Application
{
    protected $forceConfig = [];
    
    protected function __construct($options)
    {
        if(isset($options['forceConfig'])) {
            $this->forceConfig = $options['forceConfig'];
            unset($options['forceConfig']);
        }
        
        parent::__construct($options);
    }
    
    public function initConfig()
    {
        $this->config = new \BFW\test\unit\mocks\ConfigForceDatas('bfw');
        $this->forceConfig($this->forceConfig);
    }
    
    public function forceConfig($newConfig)
    {
        $this->forceConfig = $newConfig;
        $this->config->forceConfig('bfw', $this->forceConfig);
    }
}
