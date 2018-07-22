<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Application extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../../..');
        
        if ($testMethod === 'testConstructAndGetInstance') {
            return;
        }
        
        $this->createApp();
        
        $testWithAppInitialized = [
            'testGetModuleForName',
            'testGetAndDeclareRunSteps'
        ];
        
        if (in_array($testMethod, $testWithAppInitialized)) {
            $this->initApp();
        }
    }
    
    protected function addModule($moduleName, $isCore = false)
    {
        $this
            //Add the module to the mocked list
            ->if($this->app->addMockedModulesList(
                $moduleName,
                (object) [
                    'config'    => (object) [],
                    'loadInfos' => (object) []
                ]
            ))
        ;
        
        if ($isCore === true) {
            $this
                //Mock the config to add a core module
                ->given($mockedConfig = $this->app->getMockedConfigValues()['modules.php'])
                ->if($mockedConfig['modules']['controller']['name'] = $moduleName)
                ->and($mockedConfig['modules']['controller']['enabled'] = true)
                ->and($this->app->setMockedConfigValues('modules.php', $mockedConfig))
            ;
        }
        
        $this->moduleMockNativeFunctions($moduleName);
        
        return $this;
    }
    
    /**
     * Mock php native function used by readAllModules()
     * 
     * @param type $moduleName
     * @return type
     */
    protected function moduleMockNativeFunctions($moduleName = null)
    {
        if (is_null($moduleName)) {
            $this->function->scandir = ['.', '..'];
            return $this;
        }
        
        $this->function->scandir  = ['.', '..', $moduleName];
        $this->function->realpath = $moduleName;
        $this->function->is_dir   = true;
        
        return $this;
    }
    
    protected function moduleAddRunSteps($loadCore = true, $loadApp = true)
    {
        $runSteps = [
            [$this->app, 'loadAllModules']
        ];
        
        if ($loadCore === true) {
            $runSteps[] = [$this->app, 'runAllCoreModules'];
        }
        
        if ($loadApp === true) {
            $runSteps[] = [$this->app, 'runAllAppModules'];
        }
        
        return $this
            //Redefine run steps and init Application
            ->if($this->app->setRunSteps($runSteps))
        ;
    }
    
    /**
     * Test method for __constructor() and getInstance()
     * 
     * @return void
     */
    public function testConstructAndGetInstance()
    {
        $this->assert('test Constructor')
            ->object($app = \BFW\Test\Mock\Application::getInstance())
                ->isInstanceOf('\BFW\Application')
            ->object(\BFW\Test\Mock\Application::getInstance())
                ->isIdenticalTo($app)
            ->string(ini_get('default_charset'))
                ->isEqualTo('UTF-8');
        ;
    }
    
    /**
     * Test method for getComposerLoader()
     * 
     * @return void
     */
    public function testInitAndGetComposerLoader()
    {
        $this->assert('test getComposerLoader before init')
            ->variable($this->app->getComposerLoader())
                ->isNull()
        ;
        
        $this->assert('test getComposerLoader after init')
            ->if($this->initApp())
            ->object($this->app->getComposerLoader())
                ->isInstanceOf('Composer\Autoload\ClassLoader')
        ;
    }
    
    /**
     * Test method for getConfig()
     * 
     * @return void
     */
    public function testInitAndGetConfig()
    {
        $this->assert('test getConfig before init')
            ->variable($this->app->getConfig())
                ->isNull()
        ;
        
        $this->assert('test getConfig after init')
            ->if($this->initApp())
            ->object($this->app->getConfig())
                ->isInstanceOf('BFW\Config')
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
            ->object($this->app->getErrors())
                ->isInstanceOf('BFW\Core\Errors')
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
            ->object($this->app->getCli())
                ->isInstanceOf('BFW\Core\Cli')
        ;
    }
    
    /**
     * Test method for getMemcached()
     * 
     * @return void
     */
    public function testLoadAndGetMemcached()
    {
        $this->assert('test getMemcached - not call run() method')
            ->variable($this->app->getMemcached())
                ->isNull()
        ;
        
        $this->assert('test getMemcached after init - change run step for nexts tests')
            ->given($this->app->setRunSteps([
                [$this->app, 'loadMemcached']
            ]))
            ->and($this->initApp())
        ;
        
        $this->assert('test getMemcached after init - call run() method but memcached disabled')
            ->if($this->app->run())
            ->then
            ->variable($this->app->getMemcached())
                ->isNull()
        ;
        
        $this->assert('test getMemcached after init - call run() method with memcached enabled')
            ->if($config = $this->app->getConfig()->getConfigForFile('memcached.php'))
            ->and($config['memcached']['enabled'] = true)
            //We define a real memcached server, else run() return an Exception.
            ->and($config['memcached']['servers'][0]['host'] = 'localhost')
            ->and($config['memcached']['servers'][0]['port'] = 11211)
            ->and($this->app->getConfig()->setConfigForFile('memcached.php', $config))
            ->and($this->app->run())
            ->then
            ->object($this->app->getMemcached())
                ->isInstanceOf('BFW\Memcache\Memcached')
        ;
    }
    
    /**
     * Test method for getModuleList()
     * 
     * @return void
     */
    public function testInitAndGetModuleList()
    {
        $this->assert('test getModuleList before init')
            ->variable($this->app->getModuleList())
                ->isNull()
        ;
        
        $this->assert('test getModuleList after init')
            ->if($this->initApp())
            ->object($this->app->getModuleList())
                ->isInstanceOf('BFW\ModuleList')
        ;
    }
    
    /**
     * Test method for getModuleForName()
     * 
     * @return void
     */
    public function testGetModuleForName()
    {
        //Not test with existing module because we not have an installed system
        //Tested with test into the directory "test/bin".
        
        $this->assert('test getModuleForName')
            ->exception(function() {
                $this->app->getModuleForName('test');
            })
                ->hasCode(\BFW\ModuleList::ERR_NOT_FOUND)
        ;
    }
    
    /**
     * Test method for getMonolog()
     * 
     * @return void
     */
    public function testInitAndGetMonolog()
    {
        $this->assert('test getMonolog before init')
            ->variable($this->app->getMonolog())
                ->isNull()
        ;
        
        $this->assert('test getMonolog after init')
            ->if($this->initApp())
            ->given($monologHandler = $this->app->getMonolog()->getHandlers()[0])
            ->then
            ->object($this->app->getMonolog())
                ->isInstanceOf('BFW\Monolog')
            ->boolean($monologHandler->hasDebugRecords())
                ->isTrue()
            ->array($records = $monologHandler->getRecords())
                ->isNotEmpty()
            ->variable($records[0]['level'])
                ->isEqualTo(\Monolog\Logger::DEBUG)
            ->string($records[0]['formatted'])
                ->contains('Currently during the initialization framework step.')
            ->variable($records[1]['level'])
                ->isEqualTo(\Monolog\Logger::DEBUG)
            ->string($records[1]['formatted'])
                ->contains('Framework initializing done.')
        ;
    }
    
    /**
     * Test method for getOptions()
     * 
     * @return void
     */
    public function testInitAndGetOptions()
    {
        $this->assert('test getOptions before init')
            ->variable($this->app->getOptions())
                ->isNull()
        ;
        
        $this->assert('test getOptions after init')
            ->if($this->initApp())
            ->object($this->app->getOptions())
                ->isInstanceOf('BFW\Options')
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
            ->object($this->app->getRequest())
                ->isInstanceOf('BFW\Request')
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
                    ->isEqualTo(7)
        ;
        
        $this->assert('test getRunSteps content')
            ->object($runSteps[0][0])->isInstanceOf('\BFW\Application')
            ->string($runSteps[0][1])->isEqualTo('loadMemcached')
            
            ->object($runSteps[1][0])->isInstanceOf('\BFW\Application')
            ->string($runSteps[1][1])->isEqualTo('loadAllModules')
            
            ->object($runSteps[2][0])->isInstanceOf('\BFW\Application')
            ->string($runSteps[2][1])->isEqualTo('runAllCoreModules')
            
            ->object($runSteps[3][0])->isInstanceOf('\BFW\Application')
            ->string($runSteps[3][1])->isEqualTo('runAllAppModules')
            
            ->object($runSteps[4][0])->isInstanceOf('\BFW\Application')
            ->string($runSteps[4][1])->isEqualTo('runCliFile')
            
            ->object($runSteps[5][0])->isInstanceOf('\BFW\Application')
            ->string($runSteps[5][1])->isEqualTo('initCtrlRouterLink')
            
            ->object($runSteps[6][0])->isInstanceOf('\BFW\Application')
            ->string($runSteps[6][1])->isEqualTo('runCtrlRouterLink')
        ;
    }
    
    public function testInitAndGetSubjectList()
    {
        $this->assert('test getSubjectList before init')
            ->variable($this->app->getSubjectList())
                ->isNull()
        ;
        
        $this->assert('test getSubjectList after init')
            ->if($this->initApp())
            ->object($subjectList = $this->app->getSubjectList())
                ->isInstanceOf('BFW\SubjectList')
        ;
    }
    
    public function testInitConstants()
    {
        $this->assert('test initConstants before init')
            ->boolean(defined('ROOT_DIR'))
                ->isFalse()
        ;
        
        $this->assert('test initConstants after init')
            ->if($this->initApp())
            ->given($rootDir = $this->app->getOptions()->getValue('rootDir'))
            ->then
            ->boolean(defined('ROOT_DIR'))->isTrue()
                ->string(ROOT_DIR)->isEqualTo($rootDir)
            ->boolean(defined('APP_DIR'))->isTrue()
                ->string(APP_DIR)->isEqualTo($rootDir.'app/')
            ->boolean(defined('SRC_DIR'))->isTrue()
                ->string(SRC_DIR)->isEqualTo($rootDir.'src/')
            ->boolean(defined('WEB_DIR'))->isTrue()
                ->string(WEB_DIR)->isEqualTo($rootDir.'web/')
            ->boolean(defined('CONFIG_DIR'))->isTrue()
                ->string(CONFIG_DIR)->isEqualTo($rootDir.'app/config/')
            ->boolean(defined('MODULES_DIR'))->isTrue()
                ->string(MODULES_DIR)->isEqualTo($rootDir.'app/modules/')
            ->boolean(defined('CLI_DIR'))->isTrue()
                ->string(CLI_DIR)->isEqualTo($rootDir.'src/cli/')
            ->boolean(defined('CTRL_DIR'))->isTrue()
                ->string(CTRL_DIR)->isEqualTo($rootDir.'src/controllers/')
            ->boolean(defined('MODELES_DIR'))->isTrue()
                ->string(MODELES_DIR)->isEqualTo($rootDir.'src/modeles/')
            ->boolean(defined('VIEW_DIR'))->isTrue()
                ->string(VIEW_DIR)->isEqualTo($rootDir.'src/view/')
        ;
    }
    
    public function testAddComposerNamespaces()
    {
        $autoload = require(__DIR__.'/../../../../vendor/autoload.php');
        
        $this->assert('test addComposerNamespaces before init')
            ->given($prefixPsr4 = $autoload->getPrefixesPsr4())
            ->boolean(array_key_exists('Controller\\', $prefixPsr4))
                ->isFalse()
            ->boolean(array_key_exists('Modules\\', $prefixPsr4))
                ->isFalse()
            ->boolean(array_key_exists('Modeles\\', $prefixPsr4))
                ->isFalse()
        ;
        
        $this->assert('test addComposerNamespaces after init')
            ->if($this->initApp())
            ->given($prefixPsr4 = $autoload->getPrefixesPsr4())
            ->boolean(array_key_exists('Controller\\', $prefixPsr4))
                ->isTrue()
            ->boolean(array_key_exists('Modules\\', $prefixPsr4))
                ->isTrue()
            ->boolean(array_key_exists('Modeles\\', $prefixPsr4))
                ->isTrue()
        ;
    }
    
    public function testInitSessionDisabled()
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
    
    public function testInitSessionEnabled()
    {
        $this->assert('test initSession before init')
            ->variable(session_status())
                ->isNotEqualTo(PHP_SESSION_ACTIVE)
        ;
        
        $this->assert('test initSession after init')
            ->if($this->initApp(true))
            ->variable(session_status())
                ->isEqualTo(PHP_SESSION_ACTIVE)
        ;
    }
    
    /**
     * Test method for readAllModules() when there is no declared modules.
     * 
     * @return void
     */
    public function testLoadAllModulesWithoutModules()
    {
        $this->assert('test loadAllModules without modules')
            ->given($this->moduleAddRunSteps())
            ->and($this->moduleMockNativeFunctions())
            ->and($this->initApp())
            ->and($this->app->run())
            ->then
            
            ->array($this->app->getModuleList()->getLoadTree())
                ->size
                    ->isEqualTo(0);
    }
    
    /**
     * Test method for readAllModules() when there is one module without fail
     * 
     * @return void
     */
    public function testLoadAllModulesWithoutFailedModule()
    {
        $this->assert('test loadAllModules with one module')
            ->given($this->addModule('test1'))
            ->and($this->moduleAddRunSteps())
            ->and($this->initApp())
            ->and($this->app->run())
            ->then
            
            ->array($this->app->getModuleList()->getLoadTree())
                ->size
                    ->isGreaterThan(0);
        
        $this->assert('test getModuleForName')
            ->object($this->app->getModuleForName('test1'))
                ->isIdenticalTo($this->app->getModuleList()->getModuleForName('test1'));
    }
    
    /**
     * Test method for readAllModules() when there is one module with fail
     * 
     * @return void
     */
    public function testLoadAllModulesWithFailedModule()
    {
        $this->assert('test loadAllModules with one module')
            ->given($this->addModule('test1'))
            ->and($this->moduleAddRunSteps())
            ->and($this->function->is_dir = false) //<--- Not a dir. => Fail
            ->and($this->initApp())
            ->and($this->app->run())
            ->then
            
            ->array($this->app->getModuleList()->getLoadTree())
                ->size
                    ->isEqualTo(0);
    }
    
    public function testRunAllCoreModules()
    {
        $this->assert('test runAllCoreModules')
            ->given($this->addModule('test1'))
            //Load only core module, not app modules
            ->and($this->moduleAddRunSteps(true, false))
            ->and($this->initApp())
            ->and($this->app->run())
            ->then
            
            ->variable($module = $this->app->getModuleForName('test1'))
                ->isNotNull()
            ->boolean($module->isLoaded())
                ->isTrue()
        ;
    }
    
    public function testRunAllAppModules()
    {
        $this->assert('test runAllAppModules')
            ->given($this->addModule('test1'))
            //Load only app module, not core modules
            ->and($this->moduleAddRunSteps(false, true))
            ->and($this->initApp())
            ->and($this->app->run())
            ->then
            
            ->variable($module = $this->app->getModuleForName('test1'))
                ->isNotNull()
            ->boolean($module->isLoaded())
                ->isTrue()
        ;
    }
    
    public function testRunModule()
    {
        $this->assert('test runModule')
            ->given($this->addModule('test1'))
            ->and($this->moduleAddRunSteps())
            ->and($this->initApp())
            ->then
            
            //Define observer
            ->given($observer = new \BFW\Test\Helpers\ObserverArray())
            ->if($subject = $this->app->getSubjectList()->getSubjectForName('ApplicationTasks'))
            ->and($subject->attach($observer))
            ->then
            
            ->if($this->app->run())
            ->then
            
            ->variable($module = $this->app->getModuleForName('test1'))
                ->isNotNull()
            ->boolean($module->isLoaded())
                ->isTrue()
            ->boolean($module->isRun())
                ->isTrue()
            ->array($observer->getActionReceived())
                ->contains('BfwApp_load_module_test1')
        ;
    }
    
    public function testRunCliFileWhenNotCli()
    {
        $this->assert('test runCliFile if is not cli')
            ->if($this->constant->PHP_SAPI = 'www')
            ->and($this->app->setRunSteps([
                [$this->app, 'runCliFile']
            ]))
            ->and($this->initApp())
            ->then
            //No errors, no exceptions
            ->variable($this->app->run())
                ->isNull();
            //@TODO better test later with monolog integration
        ;
    }
    
    public function testRunCliFileWhenExecCliFile()
    {
        $this->assert('test runCliFile if cli file is exec')
            ->if($this->constant->PHP_SAPI = 'cli')
            //Mock native function not used because is into Cli class, not App.
            //->and($this->function->getopt = ['f' => 'example'])
            //->and($this->function->file_exists = true)
            ->and($this->app->setRunSteps([
                [$this->app, 'runCliFile']
            ]))
            ->and($this->initApp())
            ->and($this->app->getCli()->setFileInArg('/cli/example.php'))
            ->and($this->app->getCli()->setUseArgToObtainFile(false))
            ->then
            
            //Define observer
            ->given($observer = new \BFW\Test\Helpers\ObserverArray())
            ->if($subject = $this->app->getSubjectList()->getSubjectForName('ApplicationTasks'))
            ->and($subject->attach($observer))
            ->then
            
            ->if($this->app->run())
            ->string($this->app->getCli()->getExecutedFile())
                ->isEqualTo('/cli/example.php')
            ->boolean($this->app->getCli()->getIsExecuted())
                ->isTrue()
            ->array($observer->getActionReceived())
                ->contains('run_cli_file')
        ;
    }
    
    public function testInitCtrlRouterLinkInCli()
    {
        $this->assert('test initCtrlRouterLink if it\'s runned in cli')
            ->if($this->constant->PHP_SAPI = 'cli')
            ->and($this->app->setRunSteps([
                [$this->app, 'initCtrlRouterLink']
            ]))
            ->and($this->initApp())
            ->and($this->app->run())
            ->then
            
            ->variable($this->app->getCtrlRouterInfos())
                ->isNull()
            ->array($this->app->getSubjectList()->getSubjectList())
                ->notHasKey('ctrlRouterLink')
        ;
    }
    
    public function testInitCtrlRouterLinkNotInCli()
    {
        $this->assert('test initCtrlRouterLink if it\'s not runned in cli')
            ->if($this->constant->PHP_SAPI = 'www')
            ->and($this->app->setRunSteps([
                [$this->app, 'initCtrlRouterLink']
            ]))
            ->and($this->initApp())
            ->then
            
            //Define observer
            ->given($observer = new \BFW\Test\Helpers\ObserverArray())
            ->if($subject = $this->app->getSubjectList()->getSubjectForName('ApplicationTasks'))
            ->and($subject->attach($observer))
            ->and($this->app->run())
            ->then
            
            ->object($this->app->getCtrlRouterInfos())
                ->isInstanceOf('\stdClass')
            ->array($this->app->getSubjectList()->getSubjectList())
                ->hasKey('ctrlRouterLink')
            ->array($observer->getActionReceived())
                ->contains('bfw_ctrlRouterLink_subject_added')
        ;
    }
    
    protected function setRunStepsForTestRunCtrlRouterLink($observer)
    {
        return $this
            ->and($this->app->setRunSteps([
                [$this->app, 'initCtrlRouterLink'],
                function() use ($observer) {
                    try {
                        $ctrlRouterLink = $this->app->getSubjectList()->getSubjectForName('ctrlRouterLink');
                    } catch (\Exception $e) {
                        return;
                    }
                    
                    $ctrlRouterLink->attach($observer);
                },
                [$this->app, 'runCtrlRouterLink']
            ]))
        ;
    }
    
    public function testRunCtrlRouterLinkInCli()
    {
        $this->assert('test runCtrlRouterLink if it\'s runned in cli')
            ->given($observer = new \BFW\Test\Helpers\ObserverArray())
            ->if($this->constant->PHP_SAPI = 'cli')
            ->and($this->setRunStepsForTestRunCtrlRouterLink($observer))
            ->and($this->initApp())
            ->and($this->app->run())
            ->then
            
            ->array($observer->getActionReceived())
                ->notContains('ctrlRouterLink_start_run_tasks')
        ;
    }
    
    public function testRunCtrlRouterLinkNotInCli()
    {
        $this->assert('test runCtrlRouterLink if it\'s not runned in cli')
            ->given($observer = new \BFW\Test\Helpers\ObserverArray())
            ->if($this->constant->PHP_SAPI = 'www')
            ->and($this->setRunStepsForTestRunCtrlRouterLink($observer))
            ->and($this->initApp())
            ->and($this->app->run())
            ->then
            
            ->array($observer->getActionReceived())
                ->contains('ctrlRouterLink_start_run_tasks')
        ;
    }
}