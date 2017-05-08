<?php

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Modules extends atoum
{
    /**
     * @var $class Class instance
     */
    protected $class;

    /**
     * Call before each test method
     * Define CONFIG_DIR and MODULES_DIR constants
     * Instantiate the class
     * 
     * @param $testMethod string The name of the test method executed
     * 
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        define('CONFIG_DIR', 'config/');
        define('MODULES_DIR', 'modules/');
        
        $this->class = new \BFW\Modules;
    }
    
    /**
     * Test method for __constructor()
     * 
     * @return void
     */
    public function testConstructor()
    {
        $this->assert('Modules construct')
            ->object($modules = new \BFW\Modules)
                ->isInstanceOf('\BFW\Modules');
    }
    
    /**
     * Override PHP function to add a module
     * 
     * @return Object Atoum asserters
     */
    protected function addModule()
    {
        $fileGetContents = function($path) {
            if (strpos($path, '/bfwModuleInstall.json') !== false) {
                return '{
                    "srcPath": "src"
                }';
            }

            return '{}';
        };

        return $this
            ->if($this->function->file_exists = true)
            ->and($this->function->file_get_contents = $fileGetContents)
            ->and($this->function->scandir = ['.', '..', 'test.json'])
            ->then;
    }
    
    /**
     * Test method for addModule() and getModules()
     * 
     * @return void
     */
    public function testAddAndGetModule()
    {
        $this->addModule()
            ->assert('test Modules addModule')
            ->if($this->class->addModule('unit_test'))
            ->then
            ->array($getModules = $this->class->getModules())
                ->hasSize(1)
            ->object($getModules['unit_test'])
                ->isInstanceOf('\BFW\Module')
            ->object($this->class->getModule('unit_test'))
                ->isInstanceOf('\BFW\Module');
        
        $this->assert('test Modules add a second module')
            ->if($this->class->addModule('atoum'))
            ->then
            ->array($getModules = $this->class->getModules())
                ->hasSize(2)
            ->object($getModules['unit_test'])
                ->isInstanceOf('\BFW\Module')
            ->object($this->class->getModule('unit_test'))
                ->isInstanceOf('\BFW\Module')
            ->object($getModules['atoum'])
                ->isInstanceOf('\BFW\Module')
            ->object($this->class->getModule('atoum'))
                ->isInstanceOf('\BFW\Module');
    }
    
    /**
     * Test method for getModule() when it throw an exception
     * 
     * @return void
     */
    public function testGetModuleException()
    {
        $this->assert('test Modules getModule exception')
            ->given($class = $this->class)
            ->exception(function() use ($class) {
                $class->getModule('bulton');
            })->hasMessage('The Module bulton has not been found.');
    }
    
    /**
     * Generate the content of the module.json file
     * 
     * @param string $runner Value for property "runner"
     * @param string $priority Value for property "priority"
     * @param string $require Value for property "require"
     * @param string $needMe Value for property "needMe"
     * 
     * @return string
     */
    protected function generateModuleJson(
        $runner,
        $priority,
        $require,
        $needMe = ''
    ) {
        $json = '
            "runner": "'.$runner.'",
            "priority": '.$priority.',
            "require": '.$require.'
        ';
        
        if ($needMe !== '') {
            $json .= ',"needMe": '.$needMe."\n";
        }
        
        return '{'.$json.'}';
    }
    
    /**
     * Test method for generateTree() and getLoadTree()
     * 
     * @return void
     */
    public function testGenerateAndGetTree()
    {
        $module1Json = $this->generateModuleJson('', 0, '[]');
        $module2Json = $this->generateModuleJson('', 1, '[]');
        $module3Json = $this->generateModuleJson('', 1, '["module2"]');
        $module4Json = $this->generateModuleJson('', 0, '["module2"]');
        $module5Json = $this->generateModuleJson('', 1, '["module4"]');
        $module6Json = $this->generateModuleJson('', 1, '["module3", "module5"]');
        $module7Json = $this->generateModuleJson('', 3, '[]');
        $module8Json = $this->generateModuleJson('', 3, '[]');
        
        $fileGetContents = function($path) use(
            $module1Json,
            $module2Json,
            $module3Json,
            $module4Json,
            $module5Json,
            $module6Json,
            $module7Json,
            $module8Json
        ) {
            if (strpos($path, '/bfwModuleInstall.json') !== false) {
                return '{
                    "srcPath": "src"
                }';
            }
            
            if ($path === 'modules/module1/module.json') {
                return $module1Json;
            } elseif ($path === 'modules/module2/module.json') {
                return $module2Json;
            } elseif ($path === 'modules/module3/module.json') {
                return $module3Json;
            } elseif ($path === 'modules/module4/module.json') {
                return $module4Json;
            } elseif ($path === 'modules/module5/module.json') {
                return $module5Json;
            } elseif ($path === 'modules/module6/module.json') {
                return $module6Json;
            } elseif ($path === 'modules/module7/module.json') {
                return $module7Json;
            } elseif ($path === 'modules/module8/module.json') {
                return $module8Json;
            }

            return '{}';
        };
        
        $this->addModule()
            ->assert('test Module generateTree')
            ->given($this->function->file_get_contents = $fileGetContents)
            ->given($this->class->addModule('module1'))
            ->given($this->class->addModule('module2'))
            ->given($this->class->addModule('module3'))
            ->given($this->class->addModule('module4'))
            ->given($this->class->addModule('module5'))
            ->given($this->class->addModule('module6'))
            ->given($this->class->addModule('module7'))
            ->given($this->class->addModule('module8'))
            ->given($this->class->generateTree())
            ->array($tree = $this->class->getLoadTree())
                ->hasSize(3)
                ->hasKeys([0,1,3]);
        
        //The test of the array structure is into the lib's unit test.
    }
    
    /**
     * Test method for readNeedMeDependencies()
     * 
     * @return void
     */
    public function testReadNeedMeDependencies()
    {
        $module1Json = $this->generateModuleJson('', 0, '[]');
        $module2Json = $this->generateModuleJson('', 1, '[]', '["module1"]');
        $module3Json = $this->generateModuleJson('', 1, '[]', '"module1"');
        
        $fileGetContents = function($path) use(
            $module1Json,
            $module2Json,
            $module3Json
        ) {
            if (strpos($path, '/bfwModuleInstall.json') !== false) {
                return '{
                    "srcPath": "src"
                }';
            }
            
            if ($path === 'modules/module1/module.json') {
                return $module1Json;
            } elseif ($path === 'modules/module2/module.json') {
                return $module2Json;
            } elseif ($path === 'modules/module3/module.json') {
                return $module3Json;
            }

            return '{}';
        };
        
        $this->addModule()
            ->assert('test Module readNeedMeDependencies')
            ->given($this->function->file_get_contents = $fileGetContents)
            ->given($this->class->addModule('module1'))
            ->given($this->class->addModule('module2'))
            ->given($this->class->addModule('module3'))
            ->given($this->class->readNeedMeDependencies())
            ->array($this->class->getModule('module1')->getLoadInfos()->require)
                ->isEqualTo([
                    'module2',
                    'module3'
                ]);
    }
    
    /**
     * Test method for readNeedMeDependencies() when it throw an exception
     * 
     * @return void
     */
    public function testReadNeedMeDependenciesException()
    {
        $module1Json = $this->generateModuleJson('', 0, '[]');
        $module2Json = $this->generateModuleJson('', 1, '[]', '["module1"]');
        
        $fileGetContents = function($path) use(
            $module1Json,
            $module2Json
        ) {
            if (strpos($path, '/bfwModuleInstall.json') !== false) {
                return '{
                    "srcPath": "src"
                }';
            }
            
            if ($path === 'modules/module1/module.json') {
                return $module1Json;
            } elseif ($path === 'modules/module2/module.json') {
                return $module2Json;
            }

            return '{}';
        };
        
        $this->addModule()
            ->assert('test Module readNeedMeDependencies')
            ->given($this->function->file_get_contents = $fileGetContents)
            ->given($this->class->addModule('module2'))
            ->given($class = $this->class)
            ->exception(function() use ($class) {
                $class->readNeedMeDependencies();
            })
                ->hasMessage(
                    'Module error: module2 need module1 '
                    .'but the module has not been found.'
                );
    }
}
