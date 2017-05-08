<?php

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Module extends atoum
{
    /**
     * @var $class Class instance
     */
    protected $class;

    /**
     * Call before each test method
     * Define CONFIG_DIR and MODULES_DIR constants
     * 
     * @param $testMethod string The name of the test method executed
     * 
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        define('CONFIG_DIR', 'config/');
        define('MODULES_DIR', 'modules/');
    }
    
    /**
     * Test method for __constructor() without the load of the module
     * 
     * @return void
     */
    public function testConstructorWithoutLoad()
    {
        $this->assert('test constructor without load')
            ->given($class = new \BFW\Module('unit_test', false))
            ->object($class)
                ->isInstanceOf('\BFW\Module');
    }
    
    /**
     * Test method for __constructor() with the load of the module
     * 
     * @return void
     */
    public function testConstructorWithLoad()
    {
        $this->assert('test constructor with load')
            ->if($this->function->file_exists = true)
            ->and($this->function->file_get_contents = '{}')
            ->and($this->function->scandir = ['.', '..', 'test.json'])
            ->then
            ->given($class = new \BFW\Module('unit_test'))
            ->object($class)
                ->isInstanceOf('\BFW\Module');
    }
    
    /**
     * Test method for __constructor() with module.json file
     * 
     * @return void
     */
    public function testConstructorWithDescriptorFile()
    {
        $this->assert('test constructor with descriptor file')
            ->if($this->function->file_exists = function($pathFile) {
                if ($pathFile === 'modules/unit_test/module.json') {
                    return true;
                }
                
                return false;
            })
            ->and($this->function->file_get_contents = '{}')
            ->then
            ->given($class = new \BFW\Module('unit_test'))
            ->object($class)
                ->isInstanceOf('\BFW\Module');
    }
    
    /**
     * Test method for __constructor() without module.json file
     * 
     * @return void
     */
    public function testConstructorWithoutDescriptorFile()
    {
        $this->assert('test constructor without descriptor file')
            ->if($this->function->file_exists = false)
            ->then
            ->exception(function() {
                new \BFW\Module('unit_test');
            })->hasMessage('File modules/unit_test/module.json not found.');
    }
    
    /**
     * Method to generate mock php functions
     * 
     * @param array Options to configure this method
     * 
     * @return Object Atoum asserters
     */
    protected function overridePhpFunctions($options = [])
    {
        if(!isset($options['noRunner'])) {
            $options['noRunner'] = false;
        }
        
        if(!isset($options['noFileExistRunner'])) {
            $options['noFileExistRunner'] = false;
        }
        
        $fileGetContents = function($path) use ($options) {
            if ($path === 'vendor/unit/unit_test/bfwModulesInfos.json') {
                return '{
                    "srcPath": "src"
                }';
            } elseif ($path === 'modules/unit_test/module.json') {
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
                $path === 'modules/unit_test/run_unit_test_with_atoum.php' &&
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
    
    /**
     * Test method for getPathName()
     * 
     * @return void
     */
    public function testGetPathName()
    {
        $this->overridePhpFunctions()
            ->assert('test module getPathName')
            ->given($class = new \BFW\Module('unit_test'))
            ->string($class->getPathName())
                ->isEqualTo('unit_test');
    }

    /**
     * Test method for getConfig()
     * 
     * @return void
     */
    public function testGetConfig()
    {
        $this->overridePhpFunctions()
            ->assert('test module getConfig')
            ->given($class = new \BFW\Module('unit_test'))
            ->object($config = $class->getConfig())
                ->isInstanceOf('\BFW\Config')
            ->boolean($config->getConfig('unit_test'))
                ->isTrue()
            ->string($config->getConfig('lib'))
                ->isEqualTo('atoum');
    }

    /**
     * Test method for getLoadInfos()
     * 
     * @return void
     */
    public function testGetLoadInfos()
    {
        $this->overridePhpFunctions()
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

    /**
     * Test method for getStatus()
     * 
     * @return void
     */
    public function testGetStatus()
    {
        $this->overridePhpFunctions()
            ->assert('test module getStatus')
            ->given($class = new \BFW\Module('unit_test'))
            ->object($status = $class->getStatus())
            ->boolean($status->load)
                ->isTrue()
            ->boolean($status->run)
                ->isFalse();
    }

    /**
     * Test method for isLoaded()
     * 
     * @return void
     */
    public function testIsLoaded()
    {
        $this->overridePhpFunctions()
            ->assert('test module isLoaded')
            ->given($class = new \BFW\Module('unit_test'))
            ->boolean($status = $class->isLoaded())
                ->isTrue();
    }

    /**
     * Test method for isRun()
     * 
     * @return void
     */
    public function testIsRun()
    {
        $this->overridePhpFunctions()
            ->assert('test module isRun')
            ->given($class = new \BFW\Module('unit_test'))
            ->boolean($status = $class->isRun())
                ->isFalse();
    }
    
    /**
     * Test method for addDependency()
     * 
     * @return void
     */
    public function testAddDependency()
    {
        $this->overridePhpFunctions()
            ->assert('test module addDependency')
            ->given($class = new \BFW\Module('unit_test'))
            ->object($class->addDependency('module1'))
                ->isIdenticalTo($class)
            ->array($class->getLoadInfos()->require)
                ->isEqualTo(['module1'])
            ->and()
            ->given($class->addDependency('module2'))
            ->array($class->getLoadInfos()->require)
                ->isEqualTo(['module1', 'module2']);
    }
    
    /**
     * Test method for installInfos()
     * 
     * @return void
     */
    public function testInstallInfos()
    {
        $this->overridePhpFunctions()
            ->assert('test module installInfos')
            ->object($installInfos = \BFW\Module::installInfos('vendor/unit/unit_test'))
                ->isInstanceOf('\stdClass')
            ->string($installInfos->srcPath)
                ->isEqualTo('src');
    }
    
    /**
     * Test method for installInfos()
     * when there is an exceptionabout the json syntax
     * 
     * @return void
     */
    public function testInstallInfosExceptionFormat()
    {
        $this->assert('test module installInfos exception json_decode')
            ->if($this->function->file_exists = true)
            ->and($this->function->file_get_contents = function($path){
                if ($path === 'vendor/unit/unit_test/bfwModulesInfos.json') {
                    return '{
                        "srcPath": "src",
                    }';
                }
            })
            ->then
            ->exception(function() {
                \BFW\Module::installInfos('vendor/unit/unit_test');
            })->hasMessage('Syntax error');
    }
    
    /**
     * Test method for runModule() without a runner file
     * 
     * @return void
     */
    public function testRunWithoutRunnerFile()
    {
        $this->overridePhpFunctions(['noRunner' => true])
            ->assert('test module run without runner file')
            ->given($class = new \BFW\Module('unit_test'))
            ->given($class->runModule())
            ->boolean($class->isRun())
                ->isTrue();
    }
    
    /**
     * Test method for runModule() with a runner file
     * 
     * @return void
     */
    public function testRunnerFile()
    {
        $this->overridePhpFunctions()
            ->assert('test module run with runner file')
            ->given($class = new \BFW\test\unit\mocks\ModuleRunnerFile('unit_test'))
            ->string($class->callGetRunnerFile())
                ->isEqualTo('modules/unit_test/run_unit_test_with_atoum.php');
        
        $this->overridePhpFunctions(['noFileExistRunner' => true])
            ->assert('test module run with runner file exception file exists')
            ->given($class = new \BFW\test\unit\mocks\ModuleRunnerFile('unit_test'))
            ->exception(function() use ($class) {
                $class->callGetRunnerFile();
            })->hasMessage('Runner file for module unit_test not found.');
    }
}
