<?php

namespace BFW\Test\Mock\Core\AppSystems;

class Config extends \BFW\Core\AppSystems\Config
{
    protected static $mockedList = [];
    
    public static function getMockedList(): array
    {
        return self::$mockedList;
    }
    
    public static function setMockedList(string $filename, array $mockedValue)
    {
        self::$mockedList[$filename] = $mockedValue;
    }

    public function __construct()
    {
        if (self::$mockedList === null) {
            $configList = [
                'errors.php',
                'global.php',
                'memcached.php',
                'modules.php',
                'monolog.php'
            ];
            
            foreach ($configList as $configFilename) {
                self::$mockedList[$configFilename] = require(
                    $this->obtainVendorDir()
                    .'/bulton-fr/bfw/skel/app/config/bfw/'.$configFilename
                );
            }
        }
        
        $this->config = new \BFW\Config('bfw');
        foreach (self::$mockedList as $configFilename => $configValues) {
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
