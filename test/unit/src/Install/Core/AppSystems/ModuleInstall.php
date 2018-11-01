<?php

namespace BFW\Install\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class ModuleInstall extends atoum
{
    use \BFW\Test\Helpers\Install\Application;
    use \BFW\Test\Helpers\OutputBuffer;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('installAllModules')
            ->makeVisible('installModule')
        ;
        
        $this->setRootDir(__DIR__.'/../../../../../..');
        $this->createApp();

        $appSystemList = $this->app->obtainAppSystemDefaultList();
        unset($appSystemList['moduleInstall']);
        $this->app->setAppSystemToInstantiate($appSystemList);
        
        $this->initApp();
        $this->mock = new \mock\BFW\Install\Core\AppSystems\ModuleInstall;
    }
    
    public function testInvoke()
    {
        $this->assert('test Install\Core\AppSystems\ModuleInstall::__invoke')
            ->object($this->mock->__invoke())
                ->isIdenticalTo($this->mock)
        ;
    }
    
    public function testGetListToInstallAndAddToList()
    {
        $this->assert('test Install\Core\AppSystems\ModuleInstall::getListToInstall with default value')
            ->array($this->mock->getListToInstall())
                ->isEmpty()
        ;
        
        $this->assert('test Install\Core\AppSystems\ModuleInstall::addToList')
            ->given($module = new \mock\BFW\Test\Mock\Install\ModuleInstall('atoum'))
            ->and($module->setName('atoum'))
            ->then
            ->object($this->mock->addToList($module))
                ->isIdenticalTo($this->mock)
            ->array($this->mock->getListToInstall())
                ->isEqualTo([
                    'atoum' => $module
                ])
        ;
    }
    
    public function testToRun()
    {
        $this->assert('test Install\Core\AppSystems\ModuleInstall::toRun')
            ->boolean($this->mock->toRun())
                ->isTrue()
        ;
    }
    
    public function testRunAndIsRun()
    {
        $this->assert('test Install\Core\AppSystems\ModuleInstall::isRun before run')
            ->boolean($this->mock->isRun())
                ->isFalse()
        ;
        
        $this->assert('test Install\Core\AppSystems\ModuleInstall::run and isRun after')
            ->and($this->calling($this->mock)->installAllModules = null)
            ->variable($this->mock->run())
                ->isNull()
            ->boolean($this->mock->isRun())
                ->isTrue()
            ->mock($this->mock)
                ->call('installAllModules')
                    ->once()
        ;
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
                namespace BFW\Core\AppSystems;
                
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
            namespace BFW\Core\AppSystems;

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
    
    public function testInstallAllModulesWithoutModule()
    {
        $this->assert('test \Install\Application::installAllModules without module')
            ->given($lastFlushedMsg = '')
            ->and($this->defineOutputBuffer($lastFlushedMsg))
            ->then
            
            //->if($this->moduleMockNativeFunctions())
            ->and($this->mock->installAllModules())
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
            ->given($lastFlushedMsg = '')
            ->and($this->defineOutputBuffer($lastFlushedMsg))
            ->then
            
            ->if($this->moduleMockNativeFunctions('unitTest'))
            ->then
            
            ->if($module = new \BFW\Test\Mock\Install\ModuleInstall('unitTest'))
            ->and($module->setName('unitTest'))
            //->and($this->mock->addToList($module)) //Already installed
            ->and($this->app->getAppSystemList()['moduleList']->addToMockedList(
                'unitTest',
                (object) [
                    'config'    => null,
                    'loadInfos' => null
                ]
            ))
            
            ->then
            ->if($this->app->getAppSystemList()['moduleList']->run())
            ->and($this->mock->installAllModules())
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
            ->given($lastFlushedMsg = '')
            ->and($this->defineOutputBuffer($lastFlushedMsg))
            ->then
            
            ->if($this->moduleMockNativeFunctions('unitTest'))
            ->then
            
            ->if($module = new \BFW\Test\Mock\Install\ModuleInstall('unitTest'))
            ->and($module->setName('unitTest'))
            ->and($this->mock->addToList($module))
            ->and($this->app->getAppSystemList()['moduleList']->addToMockedList(
                'unitTest',
                (object) [
                    'config'    => null,
                    'loadInfos' => null
                ]
            ))
            
            ->then
            ->if($this->app->getAppSystemList()['moduleList']->run())
            ->and($this->mock->installAllModules())
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
            ->given($lastFlushedMsg = '')
            ->and($this->defineOutputBuffer($lastFlushedMsg))
            ->then
            
            ->if($this->moduleMockNativeFunctions('unitTest'))
            ->then
            
            ->given($listScripts = [])
            ->if($module = new \mock\BFW\Test\Mock\Install\ModuleInstall('unitTest'))
            ->and($this->calling($module)->runInstallScript = function($scriptName) use (&$listScripts) {
                $listScripts[] = $scriptName;
            })
            ->and($module->setName('unitTest'))
            ->and($module->setSourceInstallScript('install.php'))
            ->and($this->mock->addToList($module))
            ->and($this->app->getAppSystemList()['moduleList']->addToMockedList(
                'unitTest',
                (object) [
                    'config'    => null,
                    'loadInfos' => null
                ]
            ))
            
            ->then
            ->if($this->app->getAppSystemList()['moduleList']->run())
            ->and($this->mock->installAllModules())
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
            ->given($lastFlushedMsg = '')
            ->and($this->defineOutputBuffer($lastFlushedMsg))
            ->then
            
            ->if($this->moduleMockNativeFunctions('unitTest'))
            ->then
            
            ->given($listScripts = [])
            ->if($module = new \mock\BFW\Test\Mock\Install\ModuleInstall('unitTest'))
            ->and($this->calling($module)->runInstallScript = function($scriptName) use (&$listScripts) {
                $listScripts[] = $scriptName;
            })
            ->and($module->setName('unitTest'))
            ->and($module->setSourceInstallScript([
                'install.php',
                'checkInstall.php'
            ]))
            ->and($this->mock->addToList($module))
            ->and($this->app->getAppSystemList()['moduleList']->addToMockedList(
                'unitTest',
                (object) [
                    'config'    => null,
                    'loadInfos' => null
                ]
            ))
            
            ->then
            ->if($this->app->getAppSystemList()['moduleList']->run())
            ->and($this->mock->installAllModules())
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