<?php

namespace BFW\Core\AppSystems\Test\Mock;

class Config extends \BFW\Core\AppSystems\Config
{
    protected $mockedList = [];
    
    public function getMockedList(): array
    {
        return $this->mockedList;
    }
    
    public function setMockedList(string $filename, array $mockedValue): self
    {
        $this->mockedList[$filename] = $mockedValue;
        return $this;
    }

    public function init()
    {
        if ($this->mockedList === null) {
            $configList = [
                'errors.php',
                'global.php',
                'memcached.php',
                'modules.php',
                'monolog.php'
            ];
            
            foreach ($configList as $configFilename) {
                $this->mockedList[$configFilename] = require(
                    $this->obtainVendorDir()
                    .'/bulton-fr/bfw/skel/app/config/bfw/'.$configFilename
                );
            }
        }
        
        $this->config = new \BFW\Config('bfw');
        foreach ($this->mockedList as $configFilename => $configValues) {
            $this->config->setConfigForFilename(
                $configFilename,
                $configValues
            );
        }
    }
    
    protected function obtainVendorDir(): string
    {
        return \BFW\Application::getInstance()
            ->getOptions()
            ->getValue('vendorDir')
        ;
    }
}
