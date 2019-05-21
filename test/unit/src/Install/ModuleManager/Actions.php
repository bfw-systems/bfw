<?php

namespace BFW\Install\ModuleManager\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Actions extends atoum
{
    use \BFW\Test\Helpers\Install\Application;
    use \BFW\Test\Helpers\OutputBuffer;

    protected $mock;
    protected $manager;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('doAdd')
            ->makeVisible('doEnable')
            ->makeVisible('doDisable')
            ->makeVisible('doDelete')
            ->makeVisible('obtainModulePathList')
            ->makeVisible('searchAllModulesInDir')
            ->makeVisible('executeForModules')
            ->makeVisible('actionOnModule')
            ->makeVisible('obtainModule')
            ->makeVisible('runInstallScript')
            ->generate('BFW\Install\ModuleManager\Actions')
        ;

        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        $this->initApp();

        $this->manager = new \mock\BFW\Install\ModuleManager;

        if ($testMethod !== 'testConstructAndDefaultValues') {
            $this->mock = new \mock\BFW\Install\ModuleManager\Actions(
                $this->manager
            );
        }
    }

    public function testConstructAndDefaultValues()
    {
        $this->assert('test Install\ModuleManager\Actions::__constructor')
            ->given($this->mock = new \mock\BFW\Install\ModuleManager\Actions($this->manager))
            ->then
            ->object($this->mock->getManager())
                ->isIdenticalTo($this->manager)
        ;

        $this->assert('test Install\ModuleManager\Actions - properties default values')
            ->array($this->mock->getModulePathList())
                ->isEmpty()
            ->array($this->mock->getModuleList())
                ->isEmpty()
        ;
    }

    public function testDoAction()
    {
        $this->assert('test Install\ModuleManager\Actions::doAction - prepare')
            ->if($this->calling($this->mock)->doAdd = null)
            ->and($this->calling($this->mock)->doEnable = null)
            ->and($this->calling($this->mock)->doDisable = null)
            ->and($this->calling($this->mock)->doDelete = null)
        ;

        $this->assert('test Install\ModuleManager\Actions::doAction - add')
            ->if($this->manager->setAction('add'))
            ->then
            ->variable($this->mock->doAction())
                ->isNull()
            ->mock($this->mock)
                ->call('doAdd')->once()
                ->call('doEnable')->never()
                ->call('doDisable')->never()
                ->call('doDelete')->never()
        ;

        $this->assert('test Install\ModuleManager\Actions::doAction - enable')
            ->if($this->manager->setAction('enable'))
            ->then
            ->variable($this->mock->doAction())
                ->isNull()
            ->mock($this->mock)
                ->call('doAdd')->never()
                ->call('doEnable')->once()
                ->call('doDisable')->never()
                ->call('doDelete')->never()
        ;

        $this->assert('test Install\ModuleManager\Actions::doAction - disable')
            ->if($this->manager->setAction('disable'))
            ->then
            ->variable($this->mock->doAction())
                ->isNull()
            ->mock($this->mock)
                ->call('doAdd')->never()
                ->call('doEnable')->never()
                ->call('doDisable')->once()
                ->call('doDelete')->never()
        ;

        $this->assert('test Install\ModuleManager\Actions::doAction - delete')
            ->if($this->manager->setAction('delete'))
            ->then
            ->variable($this->mock->doAction())
                ->isNull()
            ->mock($this->mock)
                ->call('doAdd')->never()
                ->call('doEnable')->never()
                ->call('doDisable')->never()
                ->call('doDelete')->once()
        ;

        $this->assert('test Install\ModuleManager\Actions::doAction - unknown action')
            ->if($this->manager->setAction('atoum'))
            ->then
            ->variable($this->mock->doAction())
                ->isNull()
            ->mock($this->mock)
                ->call('doAdd')->never()
                ->call('doEnable')->never()
                ->call('doDisable')->never()
                ->call('doDelete')->never()
        ;
    }

    public function testDoAdd()
    {
        $this->assert('test Install\ModuleManager\Actions::doAdd - prepare')
            ->if($this->calling($this->mock)->doDelete = null)
            ->and($this->calling($this->mock)->obtainModulePathList = null)
            ->and($this->calling($this->mock)->executeForModules = null)
            ->and($this->calling($this->mock)->runInstallScript = null)
            ->then
            ->given($vendorPath = realpath($this->rootDir.'/vendor').'/')
        ;

        $this->assert('test Install\ModuleManager\Actions::doAdd - no reinstall - without module')
            ->given($this->manager->setReinstall(false))
            ->then
            ->variable($this->invoke($this->mock)->doAdd())
                ->isNull()
            ->mock($this->mock)
                ->call('doDelete')->never()
                ->call('obtainModulePathList')
                    ->withArguments($vendorPath)
                        ->once()
                ->call('executeForModules')
                    ->withArguments('doAdd', 'Add')
                        ->once()
                ->call('runInstallScript')->never()
        ;

        $this->assert('test Install\ModuleManager\Actions::doAdd - with reinstall - without module')
            ->given($this->manager->setReinstall(true))
            ->then
            ->variable($this->invoke($this->mock)->doAdd())
                ->isNull()
            ->mock($this->mock)
                ->call('doDelete')->once()
                ->call('obtainModulePathList')
                    ->withArguments($vendorPath)
                        ->once()
                ->call('executeForModules')
                    ->withArguments('doAdd', 'Add')
                        ->once()
                ->call('runInstallScript')->never()
        ;

        $this->assert('test Install\ModuleManager\Actions::doAdd - no reinstall - with module')
            ->given($this->manager->setReinstall(false))
            ->given($executeForModules = function () {
                $this->moduleList['hello-world'] = new \mock\BFW\Install\ModuleManager\Module('hello-world');
                $this->moduleList['unit-test']   = new \mock\BFW\Install\ModuleManager\Module('unit-test');
            })
            ->and($this->calling($this->mock)->executeForModules = $executeForModules->bindTo($this->mock, $this->mock))
            ->then
            ->variable($this->invoke($this->mock)->doAdd())
                ->isNull()
            ->mock($this->mock)
                ->call('doDelete')->never()
                ->call('obtainModulePathList')
                    ->withArguments($vendorPath)
                        ->once()
                ->call('executeForModules')
                    ->withArguments('doAdd', 'Add')
                        ->once()
                ->call('runInstallScript')->twice()
        ;
    }

    //Useless test :'(
    public function testDoEnable()
    {
        $this->assert('test Install\ModuleManager\Actions::doEnable')
            ->if($this->calling($this->mock)->obtainModulePathList = null)
            ->and($this->calling($this->mock)->executeForModules = null)
            ->then
            ->variable($this->invoke($this->mock)->doEnable())
                ->isNull()
            ->mock($this->mock)
                ->call('obtainModulePathList')
                    ->withArguments(MODULES_AVAILABLE_DIR)
                        ->once()
                ->call('executeForModules')
                    ->withArguments('doEnable', 'Enable')
                        ->once()
        ;
    }

    //Useless test :'(
    public function testDoDisable()
    {
        $this->assert('test Install\ModuleManager\Actions::doDisable')
            ->if($this->calling($this->mock)->obtainModulePathList = null)
            ->and($this->calling($this->mock)->executeForModules = null)
            ->then
            ->variable($this->invoke($this->mock)->doDisable())
                ->isNull()
            ->mock($this->mock)
                ->call('obtainModulePathList')
                    ->withArguments(MODULES_AVAILABLE_DIR)
                        ->once()
                ->call('executeForModules')
                    ->withArguments('doDisable', 'Disable')
                        ->once()
        ;
    }

    //Useless test :'(
    public function testDoDelete()
    {
        $this->assert('test Install\ModuleManager\Actions::doDelete')
            ->if($this->calling($this->mock)->obtainModulePathList = null)
            ->and($this->calling($this->mock)->executeForModules = null)
            ->then
            ->variable($this->invoke($this->mock)->doDelete())
                ->isNull()
            ->mock($this->mock)
                ->call('obtainModulePathList')
                    ->withArguments(MODULES_AVAILABLE_DIR)
                        ->once()
                ->call('executeForModules')
                    ->withArguments('doDelete', 'Delete')
                        ->once()
        ;
    }

    public function testObtainModulePathList()
    {
        $this->assert('test Install\ModuleManager\Actions::obtainModulePathList with module finded')
            ->if($this->calling($this->mock)->searchAllModulesInDir = [
                MODULES_AVAILABLE_DIR.'unit-test',
                MODULES_AVAILABLE_DIR.'hello-world'
            ])
            ->then
            ->variable($this->invoke($this->mock)->obtainModulePathList(MODULES_AVAILABLE_DIR))
                ->isNull()
            ->mock($this->mock)
                ->call('searchAllModulesInDir')
                    ->withArguments(MODULES_AVAILABLE_DIR)
                        ->once()
            ->array($this->mock->getModulePathList())
                ->isEqualTo([
                    'hello-world' => MODULES_AVAILABLE_DIR.'hello-world',
                    'unit-test'   => MODULES_AVAILABLE_DIR.'unit-test'
                ])
        ;
    }
    
    /*
     * Not tested because cannot mock function into ReadDirLoadModule from here
     * And it's just a call to ReadDirLoadModule::run so real test is in
     * test for class ReadDirLoadModule.
    public function testSearchAllModulesInDir()
    {
    }
    */

    public function testExecuteForModules()
    {
        $this->assert('test Install\ModuleManager\Actions::executeForModules - prepare')
            ->if($this->calling($this->mock)->actionOnModule = null)
            ->then
            ->given($setModulePathList = function () {
                $this->modulePathList = [
                    'hello-world' => MODULES_AVAILABLE_DIR.'hello-world',
                    'unit-test'   => MODULES_AVAILABLE_DIR.'unit-test'
                ];
            })
            ->and($setModulePathList = $setModulePathList->bindTo($this->mock, $this->mock))
            ->if($setModulePathList())
        ;

        $this->assert('test Install\ModuleManager\Actions::executeForModules for a specific module')
            ->if($this->manager->setSpecificModule('hello-world'))
            ->then

            ->variable($this->mock->executeForModules('doEnable', 'Enable'))
                ->isNull()
            ->mock($this->mock)
                ->call('actionOnModule')
                    ->once()
                    ->withArguments('hello-world', '', 'doEnable', 'Enable')
                        ->once()
        ;

        $this->assert('test Install\ModuleManager\Actions::executeForModules for all module')
            ->if($this->manager->setSpecificModule(''))
            ->then

            ->variable($this->mock->executeForModules('doEnable', 'Enable'))
                ->isNull()
            ->mock($this->mock)
                ->call('actionOnModule')
                    ->twice()
                    ->withArguments('hello-world', MODULES_AVAILABLE_DIR.'hello-world', 'doEnable', 'Enable')
                        ->once()
                    ->withArguments('unit-test', MODULES_AVAILABLE_DIR.'unit-test', 'doEnable', 'Enable')
                        ->once()
        ;
    }

    public function testActionOnModule()
    {
        $this->assert('test Install\ModuleManager\Actions::actionOnModule - prepare')
            ->given($mockedModule = new \mock\BFW\Install\ModuleManager\Module('osef'))
            ->if($this->calling($mockedModule)->doAdd = null)
            ->and($this->calling($mockedModule)->doEnable = null)
            ->and($this->calling($mockedModule)->doDisable = null)
            ->and($this->calling($mockedModule)->doDelete = null)
            ->then

            ->if($this->calling($this->mock)->obtainModule = $mockedModule)
            ->then

            ->given($setModulePathList = function () {
                $this->modulePathList = [
                    'hello-world' => MODULES_AVAILABLE_DIR.'hello-world',
                    'unit-test'   => MODULES_AVAILABLE_DIR.'unit-test'
                ];
            })
            ->and($setModulePathList = $setModulePathList->bindTo($this->mock, $this->mock))
            ->if($setModulePathList())
            ->then

            ->given($lastFlushedMsg = '')
            ->and($this->defineOutputBuffer($lastFlushedMsg))
        ;

        $this->assert('test Install\ModuleManager\Actions::actionOnModule - with modulePath declared')
            ->given($lastFlushedMsg = '')
            ->variable($this->invoke($this->mock)->actionOnModule(
                'hello-world',
                MODULES_AVAILABLE_DIR.'hello-world',
                'doEnable',
                'Enable'
            ))
                ->isNull()
            ->mock($mockedModule)
                ->call('setVendorPath')
                    ->withArguments(MODULES_AVAILABLE_DIR.'hello-world')
                        ->once()
                ->call('doEnable')
                    ->once()
            ->array($this->mock->getModuleList())
                ->hasKey('hello-world')
            ->object($this->mock->getModuleList()['hello-world'])
                ->isIdentiCalTo($mockedModule)
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    "\033[0;33m> Enable module hello-world ... \033[0m"
                    ."\033[0;32mDone\033[0m\n"
                )
        ;

        $this->assert('test Install\ModuleManager\Actions::actionOnModule - without modulePath declared')
            ->given($lastFlushedMsg = '')
            ->variable($this->invoke($this->mock)->actionOnModule(
                'hello-world',
                '',
                'doEnable',
                'Enable'
            ))
                ->isNull()
            ->mock($mockedModule)
                ->call('setVendorPath')
                    ->withArguments(MODULES_AVAILABLE_DIR.'hello-world')
                        ->once()
                ->call('doEnable')
                    ->once()
            ->array($this->mock->getModuleList())
                ->hasKey('hello-world')
            ->object($this->mock->getModuleList()['hello-world'])
                ->isIdentiCalTo($mockedModule)
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    "\033[0;33m> Enable module hello-world ... \033[0m"
                    ."\033[0;32mDone\033[0m\n"
                )
        ;

        $this->assert('test Install\ModuleManager\Actions::actionOnModule - without modulePath declared and for not existing module')
            ->given($lastFlushedMsg = '')
            ->exception(function () {
                $this->invoke($this->mock)->actionOnModule(
                    'atoum',
                    '',
                    'doEnable',
                    'Enable'
                );
            })
                ->hasCode(\BFW\Install\ModuleManager\Actions::EXCEP_MOD_NOT_FOUND)
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    "\033[0;33m> Enable module atoum ... \033[0m"
                ) //The error status etc is displayed by Manager
        ;

        $this->assert('test Install\ModuleManager\Actions::actionOnModule - with an error while the module action')
            ->if($this->calling($mockedModule)->doEnable = function () {
                throw new \Exception('unit-test error', 9);
            })
            ->given($lastFlushedMsg = '')
            ->variable($this->invoke($this->mock)->actionOnModule(
                'hello-world',
                MODULES_AVAILABLE_DIR.'hello-world',
                'doEnable',
                'Enable'
            ))
                ->isNull()
            ->mock($mockedModule)
                ->call('setVendorPath')
                    ->withArguments(MODULES_AVAILABLE_DIR.'hello-world')
                        ->once()
                ->call('doEnable')
                    ->once()
            ->array($this->mock->getModuleList())
                ->hasKey('hello-world')
            ->object($this->mock->getModuleList()['hello-world'])
                ->isIdentiCalTo($mockedModule)
            ->string($lastFlushedMsg)
                ->isEqualTo(
                    "\033[0;33m> Enable module hello-world ... \033[0m"
                    ."\033[1;31mERROR #9 : unit-test error\033[0m\n"
                )
        ;
    }

    public function testObtainModule()
    {
        $this->assert('test Install\ModuleManager\Actions::obtainModule')
            ->object($module = $this->mock->obtainModule('hello-world'))
                ->isInstanceOf('\BFW\Install\ModuleManager\Module')
            ->string($module->getName())
                ->isEqualTo('hello-world')
        ;
    }

    public function testRunInstallScript()
    {
        $this->assert('test Install\ModuleManager\Actions::runInstallScript - prepare')
            ->given($mockedModule = new \mock\BFW\Install\ModuleManager\Module('hello-world'))
            ->if($this->calling($mockedModule)->hasInstallScript = false)
            ->and($this->calling($mockedModule)->runInstallScript = null)
            ->then

            ->given($lastFlushedMsg = '')
            ->and($this->defineOutputBuffer($lastFlushedMsg))
        ;

        $this->assert('test Install\ModuleManager\Actions::runInstallScript - without script')
            ->if($this->calling($mockedModule)->hasInstallScript = false)
            ->given($lastFlushedMsg = '')
            ->then
            ->variable($this->mock->runInstallScript($mockedModule))
                ->isNull()
                ->string($lastFlushedMsg)
                    ->isEqualTo(
                        "\033[0;33m> Execute install script for hello-world ... \033[0m"
                        ."\033[0;33mNo script, pass.\033[0m\n"
                    )
        ;

        $this->assert('test Install\ModuleManager\Actions::runInstallScript - with a script - no error')
            ->if($this->calling($mockedModule)->hasInstallScript = true)
            ->and($this->calling($mockedModule)->runInstallScript = null)
            ->given($lastFlushedMsg = '')
            ->then
            ->variable($this->mock->runInstallScript($mockedModule))
                ->isNull()
                ->string($lastFlushedMsg)
                    ->isEqualTo(
                        "\033[0;33m> Execute install script for hello-world ... \033[0m"
                        ."\033[0;32mDone\033[0m\n"
                    )
        ;

        $this->assert('test Install\ModuleManager\Actions::runInstallScript - with a script - with error')
            ->if($this->calling($mockedModule)->hasInstallScript = true)
            ->and($this->calling($mockedModule)->runInstallScript = function () {
                throw new \Exception('for unit test', 9);
            })
            ->given($lastFlushedMsg = '')
            ->then
            ->variable($this->mock->runInstallScript($mockedModule))
                ->isNull()
                ->string($lastFlushedMsg)
                    ->isEqualTo(
                        "\033[0;33m> Execute install script for hello-world ... \033[0m"
                        ."\033[1;31mERROR #9 : for unit test\033[0m\n"
                    )
        ;
    }
}
