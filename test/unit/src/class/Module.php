<?php

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Module extends atoum
{
    /**
     * @var $class : Instance de la class
     */
    protected $class;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        define('CONFIG_DIR', 'config/');
        define('MODULES_DIR', 'modules/');
    }
    
    public function testModuleWithoutLoad()
    {
        $this->assert('test module without load')
            ->given($class = new \BFW\Module('unit_test', false))
            ->object($class)
                ->isInstanceOf('\BFW\Module');
    }
    
    public function testModuleFullConfig()
    {
        $this->assert('test module with load')
            ->if($this->function->file_exists = true)
            ->and($this->function->file_get_contents = function($path){
                if ($path === 'modules/unit_test/bfwModuleInstall.json') {
                    return '{
                        "srcPath": "src"
                    }';
                }
                
                return '{}';
            })
            ->and($this->function->scandir = ['.', '..', 'test.json'])
            ->then
            ->given($class = new \BFW\Module('unit_test'))
            ->object($class)
                ->isInstanceOf('\BFW\Module');
    }
    
    public function testModuleWithoutConfigFile()
    {
        $this->assert('test module with load')
            ->if($this->function->file_exists = function($pathFile) {
                if ($pathFile === 'modules/unit_test/bfwModuleInstall.json') {
                    return true;
                } elseif ($pathFile === 'modules/unit_test/src/module.json') {
                    return true;
                }
                
                return false;
            })
            ->and($this->function->file_get_contents = function($path) {
                if ($path === 'modules/unit_test/bfwModuleInstall.json') {
                    return '{
                        "srcPath": "src"
                    }';
                }
                
                return '{}';
            })
            ->then
            ->given($class = new \BFW\Module('unit_test'))
            ->object($class)
                ->isInstanceOf('\BFW\Module');
    }
    
    public function testModulesLoadersExceptions()
    {
        $this->assert('test module loaders exception file_exists')
            ->if($this->function->file_exists = false)
            ->then
            ->exception(function() {
                new \BFW\Module('unit_test');
            })->hasMessage('File modules/unit_test/bfwModuleInstall.json not found.');
        
        $this->assert('test module loaders exception json_decode')
            ->if($this->function->file_exists = true)
            ->and($this->function->file_get_contents = function($path){
                if ($path === 'modules/unit_test/bfwModuleInstall.json') {
                    return '{
                        "srcPath": "src"
                    }';
                }
                
                return '{,}';
            })
            ->and($this->function->scandir = ['.', '..', 'test.json'])
            ->then
            ->exception(function() {
                new \BFW\Module('unit_test');
            })->hasMessage('Syntax error');
    }
    
    protected function gettersOverloadFunctions($options = [])
    {
        if(!isset($options['noRunner'])) {
            $options['noRunner'] = false;
        }
        
        if(!isset($options['noFileExistRunner'])) {
            $options['noFileExistRunner'] = false;
        }
        
        $fileGetContents = function($path) use ($options) {
            if ($path === 'modules/unit_test/bfwModuleInstall.json') {
                return '{
                    "srcPath": "src"
                }';
            } elseif ($path === 'modules/unit_test/src/module.json') {
                $runner = '';
                if ($options['noRunner'] === false) {
                    $runner = 'run_unit_test_with_atoum.php';
                }
                
                return '{
                    "runner": "'.$runner.'",
                    "priority": 0,
                    "require": []
                }';
            } elseif ($path === 'config/unit_test/test.json') {
                return '{
                    "unit_test": true,
                    "lib": "atoum"
                }';
            }

            return '{}';
        };
        
        $fileExists = function($path) use ($options) {
            if(
                $path === 'modules/unit_test/src/run_unit_test_with_atoum.php' &&
                $options['noFileExistRunner'] === true
            ) {
                return false;
            }
            
            return true;
        };
        
        return $this
            ->if($this->function->file_exists = $fileExists)
            ->and($this->function->scandir = ['.', '..', 'test.json'])
            ->and($this->function->is_file = true)
            ->and($this->function->file_get_contents = $fileGetContents);
    }
    
    public function testGetPathName()
    {
        $this->gettersOverloadFunctions()
            ->assert('test module getPathName')
            ->given($class = new \BFW\Module('unit_test'))
            ->string($class->getPathName())
                ->isEqualTo('unit_test');
    }

    public function testGetConfig()
    {
        $this->gettersOverloadFunctions()
            ->assert('test module getConfig')
            ->given($class = new \BFW\Module('unit_test'))
            ->object($config = $class->getConfig())
                ->isInstanceOf('\BFW\Config')
            ->boolean($config->getConfig('unit_test'))
                ->isTrue()
            ->string($config->getConfig('lib'))
                ->isEqualTo('atoum');
    }

    public function testGetInstallInfos()
    {
        $this->gettersOverloadFunctions()
            ->assert('test module getInstallInfos')
            ->given($class = new \BFW\Module('unit_test'))
            ->object($installInfos = $class->getInstallInfos())
                ->isInstanceOf('\stdClass')
            ->string($installInfos->srcPath)
                ->isEqualTo('src');
    }

    public function testGetLoadInfos()
    {
        $this->gettersOverloadFunctions()
            ->assert('test module getLoadInfos')
            ->given($class = new \BFW\Module('unit_test'))
            ->object($loadInfos = $class->getLoadInfos())
                ->isInstanceOf('\stdClass')
            ->string($loadInfos->runner)
                ->isEqualTo('run_unit_test_with_atoum.php')
            ->integer($loadInfos->priority)
                ->isEqualTo(0)
            ->array($loadInfos->require)
                ->hasSize(0)
                ->isEqualTo([]);
    }

    public function testGetStatus()
    {
        $this->gettersOverloadFunctions()
            ->assert('test module getStatus')
            ->given($class = new \BFW\Module('unit_test'))
            ->object($status = $class->getStatus())
            ->boolean($status->load)
                ->isTrue()
            ->boolean($status->run)
                ->isFalse();
    }

    public function testIsLoaded()
    {
        $this->gettersOverloadFunctions()
            ->assert('test module isLoaded')
            ->given($class = new \BFW\Module('unit_test'))
            ->boolean($status = $class->isLoaded())
                ->isTrue();
    }

    public function testIsRun()
    {
        $this->gettersOverloadFunctions()
            ->assert('test module isRun')
            ->given($class = new \BFW\Module('unit_test'))
            ->boolean($status = $class->isRun())
                ->isFalse();
    }
    
    public function testInstallInfos()
    {
        $this->gettersOverloadFunctions()
            ->assert('test module installInfos')
            ->object($installInfos = \BFW\Module::installInfos('unit_test'))
                ->isInstanceOf('\stdClass')
            ->string($installInfos->srcPath)
                ->isEqualTo('src');
    }
    
    public function testRunWithoutRunnerFile()
    {
        $this->gettersOverloadFunctions(['noRunner' => true])
            ->assert('test module run without runner file')
            ->given($class = new \BFW\Module('unit_test'))
            ->given($class->runModule())
            ->boolean($class->isRun())
                ->isTrue();
    }
    
    public function testRunnerFile()
    {
        $this->gettersOverloadFunctions()
            ->assert('test module run with runner file')
            ->given($class = new MockModuleRunnerFile('unit_test'))
            ->string($class->callGetRunnerFile())
                ->isEqualTo('modules/unit_test/src/run_unit_test_with_atoum.php');
        
        $this->gettersOverloadFunctions(['noFileExistRunner' => true])
            ->assert('test module run with runner file exception file exists')
            ->given($class = new MockModuleRunnerFile('unit_test'))
            ->exception(function() use ($class) {
                $class->callGetRunnerFile();
            })->hasMessage('Runner file for module unit_test not found.');
    }
}

class MockModuleRunnerFile extends \BFW\Module
{
    public function callGetRunnerFile()
    {
        return parent::getRunnerFile();
    }
}