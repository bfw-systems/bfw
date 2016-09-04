<?php

namespace BFW\Install\test\unit\mocks;

class ModuleInstall extends \BFW\Install\ModuleInstall
{
    protected $forcedInfos = [];
    
    public function forceInfos($newInfos)
    {
        $this->forcedInfos = $newInfos;
    }
    
    protected function getInfosFromModule()
    {
        if (!is_object($this->forcedInfos)) {
            $this->forcedInfos = (object) $this->forcedInfos;
        }
        
        return $this->forcedInfos;
    }
    
    public function __get($varName)
    {
        if (!property_exists($this, $varName)) {
            throw new \Exception($varName.' is not an attribute');
        }
        
        return $this->{$varName};
    }
}
