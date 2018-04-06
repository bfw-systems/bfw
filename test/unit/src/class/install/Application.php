<?php

namespace BFW\Install\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Application extends atoum
{
    use \BFW\Install\Test\Helpers\Application;
    use \BFW\Test\Helpers\OutputBuffer;
    
    public function beforeTestMethod($testMethod)
    {
        $this->createApp();
        
        $testWithAppInitialized = [
            'testGetModuleForName',
            'testGetAndDeclareRunSteps'
        ];
        
        if (in_array($testMethod, $testWithAppInitialized)) {
            $this->initApp();
        }
    }
    
    /**
     * Mock php native function used by readAllModules()
     * 
     * @param type $moduleName
     * @return type
     */
    protected function moduleMockNativeFunctions($moduleName = null)
    {
        /*
         * Use eval like atoum core.
         * It's because native function is called into namespace \BFW and not
         * the namespace \BFw\Install, so use the atoum native function mock
         * system failed because the function is re-created into the
         * namespace \BFW\Install.
         */
        
        if (is_null($moduleName)) {
            //$this->function->scandir = ['.', '..'];
            eval('
                namespace BFW;
                
                function scandir(...$args) {
                    return [\'.\', \'..\'];
                }
            ');
            
            return $this;
        }
        
        /*
        $this->function->scandir  = ['.', '..', $moduleName];
        $this->function->realpath = $moduleName;
        $this->function->is_dir   = true;
        */
        
        eval('
            namespace BFW;

            function scandir(...$args) {
                return [\'.\', \'..\', \''.$moduleName.'\'];
            }
            function realpath(...$args) {
                return \''.$moduleName.'\';
            }
            function is_dir(...$args) {
                return true;
            }
        ');
        
        return $this;
    }
    
    /**
     * Test method for __constructor() and getInstance()
     * 
     * @return void
     */
    public function testConstructAndGetInstance()
    {
        $this->assert('test Constructor')
            ->object($app = \BFW\Install\Test\Mock\Application::getInstance())
                ->isInstanceOf('\BFW\Install\Application')
            ->object(\BFW\Install\Test\Mock\Application::getInstance())
                ->isIdenticalTo($app)
        ;
    }
    
    /**
     * Test method for getErrors()
     * 
     * @return void
     */
    public function testInitAndGetErrors()
    {
        $this->assert('test getErrors before init')
            ->variable($this->app->getErrors())
                ->isNull()
        ;
        
        $this->assert('test getErrors after init')
            ->if($this->initApp())
            ->variable($this->app->getErrors())
                ->isNull()
        ;
    }
    
    /**
     * Test method for getErrors()
     * 
     * @return void
     */
    public function testInitAndGetCli()
    {
        $this->assert('test getCli before init')
            ->variable($this->app->getCli())
                ->isNull()
        ;
        
        $this->assert('test getCli after init')
            ->if($this->initApp())
            ->variable($this->app->getCli())
                ->isNull()
        ;
    }
    
    /**
     * Test method for getRequest()
     * 
     * @return void
     */
    public function testInitAndGetRequest()
    {
        $this->assert('test getRequest before init')
            ->variable($this->app->getRequest())
                ->isNull()
        ;
        
        $this->assert('test getRequest after init')
            ->if($this->initApp())
            ->variable($this->app->getRequest())
                ->isNull()
        ;
    }
    
    /**
     * Test method for getRunSteps()
     * 
     * @return void
     */
    public function testGetAndDeclareRunSteps()
    {
        $this->assert('test getRunSteps size')
            ->array($runSteps = $this->app->getRunSteps())
                ->size
                    ->isEqualTo(3)
        ;
        
        $this->assert('test getRunSteps content')
            ->object($runSteps[0][0])->isInstanceOf('\BFW\Install\Application')
            ->string($runSteps[0][1])->isEqualTo('loadMemcached')
            
            ->object($runSteps[1][0])->isInstanceOf('\BFW\Install\Application')
            ->string($runSteps[1][1])->isEqualTo('loadAllModules')
            
            ->object($runSteps[2][0])->isInstanceOf('\BFW\Install\Application')
            ->string($runSteps[2][1])->isEqualTo('installAllModules')
        ;
    }
    
    public function testInitSession()
    {
        $this->assert('test initSession before init')
            ->variable(session_status())
                ->isNotEqualTo(PHP_SESSION_ACTIVE)
        ;
        
        $this->assert('test initSession after init')
            ->if($this->initApp())
            ->variable(session_status())
                ->isNotEqualTo(PHP_SESSION_ACTIVE)
        ;
    }
    
    public function testGetAndAddModuleInstall()
    {
        $this->assert('test \Install\Application::getModuleInstall')
            ->array(\BFW\Install\Application::getModulesInstall())
                ->isEmpty()
        ;
        
        $this->assert('test \Install\Application::addModuleInstall')
            ->if($this->initApp()) //Need constants
            ->and($module = new \BFW\Install\Test\Mock\ModuleInstall('modulePath'))
            ->and($module->setName('unitTest'))
            ->and(\BFW\Install\Application::addModuleInstall($module))
            ->then
            ->array(\BFW\Install\Application::getModulesInstall())
                ->isEqualTo([
                    'unitTest' => $module
                ])
        ;
    }
    
    public function testInstallAllModulesWithoutModule()
    {
        $this->assert('test \Install\Application::installAllModules without module')
            ->given($defineOutputBuffer = '')
            ->and($this->defineOutputBuffer($lastFlushedMsg))
            ->then
            
            ->if($this->moduleMockNativeFunctions())
            ->and($this->app->setRunSteps([
                [$this->app, 'loadAllModules'],
                [$this->app, 'installAllModules'],
            ]))
            ->and($this->initApp())
            ->and($this->app->run())
            ->then
            
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    'Read all modules to run install script...'."\n"
                    .'All modules have been read.'."\n"
                )
        ;
    }
    
    public function testInstallAllModulesWithAlreadyInstalledModule()
    {
        $this->assert('test \Install\Application::installAllModules with already installed module')
            ->given($defineOutputBuffer = '')
            ->and($this->defineOutputBuffer($lastFlushedMsg))
            ->then
            
            ->if($this->moduleMockNativeFunctions('unitTest'))
            ->and($this->app->setRunSteps([
                [$this->app, 'loadAllModules'],
                [$this->app, 'installAllModules'],
            ]))
            ->and($this->initApp())
            ->then
            
            ->if($module = new \BFW\Install\Test\Mock\ModuleInstall('unitTest'))
            ->and($module->setName('unitTest'))
            
            ->then
            ->and($this->app->run())
            ->then
            
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    'Read all modules to run install script...'."\n"
                    .'All modules have been read.'."\n"
                )
        ;
    }
    
    public function testInstallModuleWithoutInstallScript()
    {
        $this->assert('test \Install\Application::installModule without install script')
            ->given($defineOutputBuffer = '')
            ->and($this->defineOutputBuffer($lastFlushedMsg))
            ->then
            
            ->if($this->moduleMockNativeFunctions('unitTest'))
            ->and($this->app->setRunSteps([
                [$this->app, 'loadAllModules'],
                [$this->app, 'installAllModules'],
            ]))
            ->and($this->initApp())
            ->then
            
            ->if($module = new \BFW\Install\Test\Mock\ModuleInstall('unitTest'))
            ->and($module->setName('unitTest'))
            ->and(\BFW\Install\Application::addModuleInstall($module))
            
            ->then
            ->and($this->app->run())
            ->then
            
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    'Read all modules to run install script...'."\n"
                    .' > Read for module unitTest'."\n"
                    .' >> No script to run.'."\n"
                    .'All modules have been read.'."\n"
                )
        ;
    }
    
    public function testInstallModuleWithOneInstallScript()
    {
        $this->assert('test \Install\Application::installModule with one install script')
            ->given($defineOutputBuffer = '')
            ->and($this->defineOutputBuffer($lastFlushedMsg))
            ->then
            
            ->if($this->moduleMockNativeFunctions('unitTest'))
            ->and($this->app->setRunSteps([
                [$this->app, 'loadAllModules'],
                [$this->app, 'installAllModules'],
            ]))
            ->and($this->initApp())
            ->then
            
            ->given($listScripts = [])
            ->if($module = new \mock\BFW\Install\Test\Mock\ModuleInstall('unitTest'))
            ->and($this->calling($module)->runInstallScript = function($scriptName) use (&$listScripts) {
                $listScripts[] = $scriptName;
            })
            ->and($module->setName('unitTest'))
            ->and($module->setSourceInstallScript('install.php'))
            ->and(\BFW\Install\Application::addModuleInstall($module))
            
            ->then
            ->and($this->app->run())
            ->then
            
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    'Read all modules to run install script...'."\n"
                    .' > Read for module unitTest'."\n"
                    .'All modules have been read.'."\n"
                )
            ->array($listScripts)
                ->isEqualTo([
                    'install.php'
                ])
        ;
    }
    
    public function testInstallModuleWithTwoInstallScript()
    {
        $this->assert('test \Install\Application::installModule with one install script')
            ->given($defineOutputBuffer = '')
            ->and($this->defineOutputBuffer($lastFlushedMsg))
            ->then
            
            ->if($this->moduleMockNativeFunctions('unitTest'))
            ->and($this->app->setRunSteps([
                [$this->app, 'loadAllModules'],
                [$this->app, 'installAllModules'],
            ]))
            ->and($this->initApp())
            ->then
            
            ->given($listScripts = [])
            ->if($module = new \mock\BFW\Install\Test\Mock\ModuleInstall('unitTest'))
            ->and($this->calling($module)->runInstallScript = function($scriptName) use (&$listScripts) {
                $listScripts[] = $scriptName;
            })
            ->and($module->setName('unitTest'))
            ->and($module->setSourceInstallScript([
                'install.php',
                'checkInstall.php'
            ]))
            ->and(\BFW\Install\Application::addModuleInstall($module))
            
            ->then
            ->and($this->app->run())
            ->then
            
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    'Read all modules to run install script...'."\n"
                    .' > Read for module unitTest'."\n"
                    .'All modules have been read.'."\n"
                )
            ->array($listScripts)
                ->isEqualTo([
                    'install.php',
                    'checkInstall.php'
                ])
        ;
    }
}