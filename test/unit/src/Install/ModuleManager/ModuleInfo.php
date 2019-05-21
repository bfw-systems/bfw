<?php

namespace BFW\Install\ModuleManager\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class ModuleInfo extends atoum
{
    protected $mock;
    
    protected $info;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('convertValues')
            ->makeVisible('convertConfigFiles')
            ->makeVisible('convertInstallScript')
            ->generate('BFW\Install\ModuleManager\ModuleInfo')
        ;
        
        $this->info = (object) [
            'srcPath'       => 'src/',
            'configFiles'   => ['myConfig.php', 'test.json'],
            'configPath'    => 'config/',
            'installScript' => 'install.php'
        ];

        if ($testMethod === 'testConstructAndDefaultValues') {
            return;
        }

        $this->mock = new \mock\BFW\Install\ModuleManager\ModuleInfo($this->info);
    }

    public function testConstructAndDefaultValues()
    {
        $this->assert('test Install\ModuleManager\ModuleInfo::__construct')
            ->given($this->mock = new \mock\BFW\Install\ModuleManager\ModuleInfo($this->info))
            ->then
            ->object($this->mock->getInfo())
                ->isIdenticalTo($this->info)
            ->string($this->mock->getSrcPath())
                ->isEqualTo('src/')
            ->array($this->mock->getConfigFiles())
                ->isEqualTo(['myConfig.php', 'test.json'])
            ->string($this->mock->getConfigPath())
                ->isEqualTo('config/')
            ->string($this->mock->getInstallScript())
                ->isEqualTo('install.php')
        ;
    }

    public function testConvertValues()
    {
        //Just call others convert methods, I seriously need to test that ?!
    }

    public function testConvertConfigFiles()
    {
        $this->assert('test Install\ModuleManager\ModuleInfo::convertConfigFiles - prepare')
            ->given($setConfigFiles = function ($configFiles) {
                $this->configFiles = $configFiles;
            })
            ->and($setConfigFiles = $setConfigFiles->bindTo($this->mock, $this->mock))
        ;

        $this->assert('test Install\ModuleManager\ModuleInfo::convertConfigFiles - with array')
            ->if($setConfigFiles([]))
            ->then
            ->variable($this->mock->convertConfigFiles())
                ->isNull()
            ->array($this->mock->getConfigFiles())
                ->isEqualTo([])
        ;

        $this->assert('test Install\ModuleManager\ModuleInfo::convertConfigFiles - with string value')
            ->if($setConfigFiles('myConfigFile.php'))
            ->then
            ->variable($this->mock->convertConfigFiles())
                ->isNull()
            ->array($this->mock->getConfigFiles())
                ->isEqualTo(['myConfigFile.php'])
        ;

        $this->assert('test Install\ModuleManager\ModuleInfo::convertConfigFiles - with null value')
            ->if($setConfigFiles(null))
            ->then
            ->variable($this->mock->convertConfigFiles())
                ->isNull()
            ->array($this->mock->getConfigFiles())
                ->isEqualTo([])
        ;

        $this->assert('test Install\ModuleManager\ModuleInfo::convertConfigFiles - with true value')
            ->if($setConfigFiles(true))
            ->then
            ->variable($this->mock->convertConfigFiles())
                ->isNull()
            ->array($this->mock->getConfigFiles())
                ->isEqualTo([])
        ;
    }

    public function testConvertInstallScript()
    {
        $this->assert('test Install\ModuleManager\ModuleInfo::convertInstallScript - prepare')
            ->given($setInstallScript = function ($installScript) {
                $this->installScript = $installScript;
            })
            ->and($setInstallScript = $setInstallScript->bindTo($this->mock, $this->mock))
        ;

        $this->assert('test Install\ModuleManager\ModuleInfo::convertInstallScript - with true value')
            ->if($setInstallScript(true))
            ->then
            ->variable($this->mock->convertInstallScript())
                ->isNull()
            ->string($this->mock->getInstallScript())
                ->isEqualTo('runInstallModule.php')
        ;

        $this->assert('test Install\ModuleManager\ModuleInfo::convertInstallScript - with string value')
            ->if($setInstallScript('install.php'))
            ->then
            ->variable($this->mock->convertInstallScript())
                ->isNull()
            ->string($this->mock->getInstallScript())
                ->isEqualTo('install.php')
        ;

        $this->assert('test Install\ModuleManager\ModuleInfo::convertInstallScript - with null value')
            ->if($setInstallScript(null))
            ->then
            ->variable($this->mock->convertInstallScript())
                ->isNull()
            ->string($this->mock->getInstallScript())
                ->isEqualTo('')
        ;

        $this->assert('test Install\ModuleManager\ModuleInfo::convertInstallScript - with false value')
            ->if($setInstallScript(false))
            ->then
            ->variable($this->mock->convertInstallScript())
                ->isNull()
            ->string($this->mock->getInstallScript())
                ->isEqualTo('')
        ;
    }
}
