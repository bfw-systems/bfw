<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Module extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../..');
        $this->createApp();
        $this->initApp();
        
        $this->mockGenerator
            ->makeVisible('loadConfig')
            ->makeVisible('obtainLoadInfos')
            ->makeVisible('readJsonFile')
            ->makeVisible('obtainRunnerFile')
            ->generate('BFW\Module')
        ;
        
        if ($testMethod == 'testConstruct') {
            return;
        }
        
        $this->mock = new \mock\BFW\Module('atoum');
    }
    
    protected function mockLoadJsonFile($filePath, $json)
    {
        $this->given($this->function->file_exists = function($path) use (&$filePath) {
                return ($path === $filePath);
            })
            ->given($this->function->file_get_contents = function($path) use (&$filePath, $json) {
                if ($path !== $filePath) {
                    return false;
                }
                
                return $json;
            })
        ;
    }
    
    public function testConstruct()
    {
        $this->assert('test Module::__construct and getPathName')
            ->object($this->mock = new \mock\BFW\Module('atoum'))
                ->isInstanceOf('\BFW\Module')
            ->string($this->mock->getPathName())
                ->isEqualTo('atoum')
            ->object($status = $this->mock->getStatus())
                ->string(get_class($status))
                    ->contains('class@anonymous')
            ->boolean(property_exists($status, 'load'))
                ->isTrue()
            ->boolean(property_exists($status, 'run'))
                ->isTrue()
            ->boolean($status->load)
                ->isFalse()
            ->boolean($status->run)
                ->isFalse()
        ;
    }
    
    public function testLoadModule()
    {
        $this->assert('test Module::loadModule')
            ->if($this->calling($this->mock)->loadConfig = null)
            ->and($this->calling($this->mock)->obtainLoadInfos = null)
            ->then
            ->variable($this->invoke($this->mock)->loadModule())
                ->isNull()
            ->boolean($this->mock->getStatus()->load)
                ->isTrue()
        ;
    }
    
    public function testInstallInfos()
    {
        $this->assert('test Module::installInfos')
            ->given($mock = $this->mock)
        /* Can't mock static method :'(
            ->given($moduleInfos = new \stdClass)
            ->if($this->calling($this->mock)->loadJsonFile = $moduleInfos)
        */
            ->given($this->mockLoadJsonFile(
                ROOT_DIR.'vendor/unknown/mymodule/bfwModulesInfos.json',
                '{
                    "srcPath": "src/",
                    "configPath": "config/",
                    "configFiles": [
                        "mymodule.json"
                    ],
                    "installScript": ""
                }'
            ))
            ->then
            ->object($mock::installInfos(ROOT_DIR.'vendor/unknown/mymodule'))
                ->isEqualTo((object) [
                    'srcPath'       => 'src/',
                    'configPath'    => 'config/',
                    'configFiles'   => ['mymodule.json'],
                    'installScript' => ''
                ])
        ;
    }
    
    public function testGetPathName()
    {
        $this->assert('test Module::getPathName')
            ->string($this->mock->getPathName())
                ->isEqualTo('atoum')
        ;
    }
    
    public function testLoadAndGetConfig()
    {
        $this->assert('test Module::getConfig without load')
            ->variable($this->mock->getConfig())
                ->isNull()
        ;
        
        $this->assert('test Module::loadConfig without a config directory')
            ->if($this->function->file_exists = false)
            ->and($this->mock->loadConfig())
            ->then
            ->variable($this->mock->getConfig())
                ->isNull()
        ;
        
        $this->assert('test Module::loadConfig with a config directory')
            ->given($fileExistsNbCalls = 0)
            ->if($this->function->file_exists = function($path) use (&$fileExistsNbCalls) {
                $fileExistsNbCalls++;
                
                if ($path === CONFIG_DIR.'atoum' && $fileExistsNbCalls === 1) {
                    return true;
                }
                
                return false;
            })
            ->and($this->mock->loadConfig())
            ->then
            ->object($this->mock->getConfig())
                ->isInstanceOf('\BFW\Config')
        ;
    }
    
    public function testObtainAndGetLoadInfos()
    {
        $this->assert('test Module::getLoadInfos without load')
            ->variable($this->mock->getLoadInfos())
                ->isNull()
        ;
        
        $this->assert('test Module::loadInfos')
            //Can't mock directly a static method (like loadJsonFile) :'(
            ->given($this->mockLoadJsonFile(
                MODULES_DIR.$this->mock->getPathName().'/module.json',
                '{
                    "runner": "mymodule.php",
                    "priority": 0,
                    "require": []
                }'
            ))
            ->and($this->invoke($this->mock)->obtainLoadInfos())
            ->then
            ->object($this->mock->getLoadInfos())
                ->isEqualTo((object) [
                    'runner'   => 'mymodule.php',
                    'priority' => 0,
                    'require'  => []
                ])
        ;
    }
    
    public function testGetStatus()
    {
        $this->mock = new \BFW\Test\Mock\Module('atoum');
        
        $this->assert('test Module::getStatus for default value')
            ->object($status = $this->mock->getStatus())
                ->string(get_class($status))
                    ->contains('class@anonymous')
            ->boolean($status->load)
                ->isFalse()
            ->boolean($status->run)
                ->isFalse()
        ;
        
        $this->assert('test Module::getStatus with load to true')
            ->if($this->mock->setStatus(true, false))
            ->then
            ->object($status = $this->mock->getStatus())
            ->boolean($status->load)
                ->isTrue()
            ->boolean($status->run)
                ->isFalse()
        ;
        
        $this->assert('test Module::getStatus with run to true')
            ->if($this->mock->setStatus(true, true))
            ->then
            ->object($status = $this->mock->getStatus())
            ->boolean($status->load)
                ->isTrue()
            ->boolean($status->run)
                ->isTrue()
        ;
    }
    
    public function testIsLoaded()
    {
        $this->mock = new \BFW\Test\Mock\Module('atoum');
        
        $this->assert('test Module::isLoaded when is not loaded')
            ->boolean($this->mock->isLoaded())
                ->isFalse()
        ;
        
        $this->assert('test Module::isLoaded when is loaded')
            ->if($this->mock->setStatus(true, false))
            ->then
            ->boolean($this->mock->isLoaded())
                ->isTrue()
        ;
    }
    
    public function testIsRun()
    {
        $this->mock = new \BFW\Test\Mock\Module('atoum');
        
        $this->assert('test Module::isRun when is not runned')
            ->boolean($this->mock->isRun())
                ->isFalse()
        ;
        
        $this->assert('test Module::isRun when is runned')
            ->if($this->mock->setStatus(false, true))
            ->then
            ->boolean($this->mock->isRun())
                ->isTrue()
        ;
    }
    
    public function testReadJsonFile()
    {
        $this->mock = new \BFW\Test\Mock\Module('atoum');
        
        $this->assert('test Modulle::readJsonFile without file')
            ->if($this->function->file_exists = false)
            ->then
            ->exception(function() {
                $this->mock->callReadJsonFile(MODULES_DIR.'atoum/module.json');
            })
                ->hasCode(\BFW\Module::ERR_FILE_NOT_FOUND)
        ;
        
        $this->assert('test Modulle::readJsonFile with a bad json')
            ->if($this->function->file_exists = true)
            ->and($this->function->file_get_contents = '{"runner": "helloWorld.php",')
            ->then
            ->exception(function() {
                $this->mock->callReadJsonFile(MODULES_DIR.'atoum/module.json');
            })
                ->hasCode(\BFW\Module::ERR_JSON_PARSE)
                ->message
                    ->isNotEmpty()
        ;
        
        $this->assert('test Modulle::readJsonFile with a correct json')
            ->if($this->function->file_exists = true)
            ->and($this->function->file_get_contents = '{
                    "runner": "mymodule.php",
                    "priority": 0,
                    "require": []
                }'
            )
            ->then
            ->object($this->mock->callReadJsonFile(MODULES_DIR.'atoum/module.json'))
                ->isEqualTo((object) [
                    'runner'   => 'mymodule.php',
                    'priority' => 0,
                    'require'  => []
                ])
        ;
    }
    
    public function testAddDependency()
    {
        $this->mock = new \BFW\Test\Mock\Module('atoum');
        
        $this->assert('test Module::addDependency with "require" into loadInfos')
            ->given($this->mock->setLoadInfos((object) [
                'require' => []
            ]))
            ->if($this->mock->addDependency('unitTest'))
            ->then
            ->object($this->mock->getLoadInfos())
                ->isEqualTo((object) [
                    'require' => ['unitTest']
                ])
        ;
        
        $this->assert('test Module::addDependency with "require" into loadInfos but not as array')
            ->given($this->mock->setLoadInfos((object) [
                'require' => 'hello-world'
            ]))
            ->if($this->mock->addDependency('unitTest'))
            ->then
            ->object($this->mock->getLoadInfos())
                ->isEqualTo((object) [
                    'require' => ['hello-world', 'unitTest']
                ])
        ;
        
        $this->assert('test Module::addDependency without "require" into loadInfos')
            ->given($this->mock->setLoadInfos(new \stdClass))
            ->if($this->mock->addDependency('unitTest'))
            ->then
            ->object($this->mock->getLoadInfos())
                ->isEqualTo((object) [
                    'require' => ['unitTest']
                ])
        ;
    }
    
    public function testObtainRunnerFile()
    {
        $this->mockGenerator
            ->makeVisible('obtainRunnerFile')
            ->generate('BFW\Test\Mock\Module')
        ;
        
        $this->mock = new \mock\BFW\Test\Mock\Module('atoum');
        
        $this->assert('test Module::obtainRunnerFile without property "runner"')
            ->string($this->invoke($this->mock)->obtainRunnerFile())
                ->isEmpty()
        ;
        
        $this->assert('test Module::obtainRunnerFile with empty property "runner"')
            ->given($this->mock->setLoadInfos((object) ['runner' => '']))
            ->string($this->invoke($this->mock)->obtainRunnerFile())
                ->isEmpty()
        ;
        
        $this->assert('test Module::obtainRunnerFile without runner file')
            ->given($this->mock->setLoadInfos((object) ['runner' => 'run_atoum.php']))
            ->and($this->function->file_exists = false)
            ->exception(function() {
                $this->invoke($this->mock)->obtainRunnerFile();
            })
                ->hasCode(\BFW\Module::ERR_RUNNER_FILE_NOT_FOUND)
        ;
        
        $this->assert('test Module::obtainRunnerFile with runner file')
            ->given($this->mock->setLoadInfos((object) ['runner' => 'run_atoum.php']))
            ->and($this->function->file_exists = true)
            ->string($this->invoke($this->mock)->obtainRunnerFile())
                ->isEqualTo(MODULES_DIR.$this->mock->getPathName().'/run_atoum.php')
        ;
    }
    
    public function testRunModule()
    {
        $this->mockGenerator
            ->makeVisible('obtainRunnerFile')
            ->generate('BFW\Test\Mock\Module')
        ;
        
        $this->mock = new \mock\BFW\Test\Mock\Module('atoum');
        
        $this->assert('test Module::runModule if the module is already runned')
            ->if($this->mock->setStatus(true, true))
            ->and($this->calling($this->mock)->obtainRunnerFile = '')
            ->then
            ->variable($this->mock->runModule())
                ->isNull()
            ->mock($this->mock)
                ->call('obtainRunnerFile')
                    ->never()
            ->boolean($this->mock->isRun())
                ->isTrue()
        ;
        
        $this->assert('test Module::runModule without file to run')
            ->if($this->mock->setStatus(true, false))
            ->and($this->calling($this->mock)->obtainRunnerFile = '')
            ->then
            ->variable($this->mock->runModule())
                ->isNull()
            ->mock($this->mock)
                ->call('obtainRunnerFile')
                    ->once()
            ->boolean($this->mock->isRun())
                ->isTrue()
        ;
        
        //Require not mockable, so we can't test with file to execute.
    }
}