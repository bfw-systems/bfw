<?php

namespace BFW\test\unit;

use \atoum;
use \BFW\test\unit\mocks\Application as MockApp;
use \BFW\test\unit\mocks\Observer as MockObserver;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Application extends atoum
{
    /**
     * @var $mock Mock instance
     */
    protected $mock;
    
    /**
     * @var array $forcedConfig Config used for all test into this file
     */
    protected $forcedConfig;

    /**
     * Call before each test method
     * Define forced config
     * Remove existing BFW Application instance
     * Instantiate the mock
     * 
     * @param $testMethod string The name of the test method executed
     * 
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        $this->forcedConfig = require(__DIR__.'/../../helpers/applicationConfig.php');
        
        MockApp::removeInstance();
        
        if ($testMethod === 'testConstructor') {
            return;
        }
        
        //All condition is on the test method.
        //If I put all condition here => no effect oO
        if ($testMethod === 'testInitSessionDisabled') {
            return;
        }
        
        $options = [
            'forceConfig'     => $this->forcedConfig,
            'vendorDir'       => __DIR__.'/../../../../vendor',
            'testOption'      => 'unit test',
            'overrideMethods' => [
                'runCliFile'     => null,
                'initModules'    => function() {
                    $this->modules = new \BFW\test\unit\mocks\Modules;
                },
                'readAllModules' => function() {
                    $modules = $this->modules;
                    foreach($this->modulesToAdd as $moduleName => $module) {
                        $modules::setModuleConfig($moduleName, $module->config);
                        $modules::setModuleLoadInfos($moduleName, $module->loadInfos);
                    }

                    parent::readAllModules();
                }
            ]
        ];
        
        if ($testMethod === 'testInitSessionDisabled') {
            $options['runSession'] = false;
        }
        
        $this->function->scandir = ['.', '..']; //used by test which call run()
        $this->mock = MockApp::init($options);
    }
    
    /**
     * Test method for __constructor()
     * 
     * @return void
     */
    public function testConstructor()
    {
        $this->assert('test Constructor')
            ->object($app = MockApp::init([
                'forceConfig'        => $this->forcedConfig,
                'vendorDir'          => __DIR__.'/../../../../vendor',
                'overrideAllMethods' => true
            ]))
                ->isInstanceOf('\BFW\Application')
            ->object(MockApp::getInstance())
                ->isEqualTo($app);
    }
    
    /**
     * Test method for getComposerLoader()
     * 
     * @return void
     */
    public function testGetComposerLoader()
    {
        $this->assert('test getComposerLoader')
            ->object($this->mock->getComposerLoader())
                ->isInstanceOf('Composer\Autoload\ClassLoader');
    }
    
    /**
     * Test method for getConfig()
     * 
     * @return void
     */
    public function testGetConfig()
    {
        $this->assert('test getConfig')
            ->boolean($this->mock->getConfig()->getValue('debug'))
                ->isFalse()
            ->array($this->mock->getConfig()->getValue('errorRenderFct'))
                ->isEqualto([
                    'enabled' => false,
                    'default' => [
                        'class'  => '',
                        'method' => ''
                    ],
                    'cli'     => [
                        'class'  => '',
                        'method' => ''
                    ]
                ]);
        
        $this->assert('test getConfig exception')
            ->given($app = $this->mock)
            ->exception(function() use ($app) {
                $app->getConfig()->getValue('unitTest');
            })
                ->hasMessage('The config key unitTest has not been found');
    }
    
    /**
     * Test method for getOption()
     * 
     * @return void
     */
    public function testGetOption()
    {
        $this->assert('test getOption')
            ->string($this->mock->getOption('testOption'))
                ->isEqualTo('unit test');
        
        $this->assert('test getOption exception')
            ->given($app = $this->mock)
            ->exception(function() use ($app) {
                $app->getOption('testNotExist');
            })
                ->hasMessage('Option key testNotExist not exist.');
    }
    
    /**
     * Test method for getRequest()
     * 
     * @return void
     */
    public function testGetRequest()
    {
        $this->assert('test getRequest')
            ->object($request = $this->mock->getRequest())
                ->isInstanceOf('\BFW\Request');
    }
    
    /**
     * Test method for initOptions()
     * 
     * @return void
     */
    public function testInitOptions()
    {
        //rootdir back 5 directories. He think to be in the vendor.
        $rootDir = dirname(dirname(realpath(__DIR__.'/../../../../'))).'/';
        
        $this->assert('test initOptions')
            ->string($this->mock->getOption('rootDir'))
                ->isEqualTo($rootDir)
            ->string($this->mock->getOption('vendorDir'))
                ->isEqualTo(__DIR__.'/../../../../vendor/')
            ->boolean($this->mock->getOption('runSession'))
                ->isTrue();
    }
    
    /**
     * Test method for initConstants()
     * 
     * @return void
     */
    public function testInitConstants()
    {
        //rootdir back 5 directories. He think to be in the vendor.
        $rootDir = dirname(dirname(realpath(__DIR__.'/../../../../'))).'/';
        
        $this->assert('test constants ROOT_DIR')
            ->string(ROOT_DIR)
                ->isEqualTo($rootDir);
        
        $this->assert('test constants APP_DIR')
            ->string(APP_DIR)
                ->isEqualTo($rootDir.'app/');
        
        $this->assert('test constants SRC_DIR')
            ->string(SRC_DIR)
                ->isEqualTo($rootDir.'src/');
        
        $this->assert('test constants WEB_DIR')
            ->string(WEB_DIR)
                ->isEqualTo($rootDir.'web/');
        
        $this->assert('test constants CONFIG_DIR')
            ->string(CONFIG_DIR)
                ->isEqualTo($rootDir.'app/config/');
        
        $this->assert('test constants MODULES_DIR')
            ->string(MODULES_DIR)
                ->isEqualTo($rootDir.'app/modules/');
        
        $this->assert('test constants CLI_DIR')
            ->string(CLI_DIR)
                ->isEqualTo($rootDir.'src/cli/');
        
        $this->assert('test constants CTRL_DIR')
            ->string(CTRL_DIR)
                ->isEqualTo($rootDir.'src/controllers/');
        
        $this->assert('test constants MODELES_DIR')
            ->string(MODELES_DIR)
                ->isEqualTo($rootDir.'src/modeles/');
        
        $this->assert('test constants VIEW_DIR')
            ->string(VIEW_DIR)
                ->isEqualTo($rootDir.'src/view/');
    }
    
    /**
     * test method for initComposerLoader
     * Done by test getComposerLoader and addComposerNamespaces
     * 
     * @return void
     */
    public function testInitComposerLoader()
    {
        
    }
    
    /**
     * Test method for initConfig
     * Done by getConfig
     * Not in coverrage because mocking.
     * 
     * @return void
     */
    public function testInitConfig()
    {
        
    }
    
    /**
     * Test method for initRequest
     * Done by getRequest
     * 
     * @return void
     */
    public function testInitRequest()
    {
        
    }
    
    /**
     * Test method for initSessionEnabled()
     * 
     * @return void
     */
    public function testInitSessionEnabled()
    {
        $this->assert('test initSession enabled')
            ->string(session_id())
                ->isNotEmpty();
    }
    
    /**
     * Test method for initSessionDisabled()
     * 
     * @return void
     */
    public function testInitSessionDisabled()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        //Strange to be forced to put that here !!
        //beforeTestMethod should be doing this.
        $this->assert('test initSession disabled')
            ->if(MockApp::removeInstance())
            ->and($this->mock = MockApp::init([
                'forceConfig' => $this->forcedConfig,
                'vendorDir'   => __DIR__.'/../../../../vendor',
                'runSession'  => false
            ]))
            ->then
            ->string(session_id())
                ->isEmpty();
    }
    
    /**
     * Test method for initErrors()
     * 
     * @return void
     */
    public function testInitErrors()
    {
        $this->assert('test initErrors')
            ->object($this->mock->getErrors())
                ->isInstanceOf('\BFW\Core\Errors');
    }
    
    /**
     * Test method for initModules()
     * 
     * @return void
     */
    public function testInitModules()
    {
        $this->assert('test initModules')
            ->object($this->mock->getModules())
                ->isInstanceOf('\BFW\Modules');
    }
    
    /**
     * Test method for addComposerNamespaces()
     * 
     * @return void
     */
    public function testAddComposerNamespaces()
    {
        $this->assert('test addComposerNamespaces')
            ->array($loaderPrefixes = $this->mock->getComposerLoader()->getPrefixesPsr4())
                ->hasKeys([
                    'Controller\\',
                    'Modules\\',
                    'Modeles\\'
                ])
            ->array($loaderPrefixes['Controller\\'])
                ->size->isEqualTo(1)
            ->array($loaderPrefixes['Modules\\'])
                ->size->isEqualTo(1)
            ->array($loaderPrefixes['Modeles\\'])
                ->size->isEqualTo(1)
            ->string($loaderPrefixes['Controller\\'][0])
                ->isEqualTo(CTRL_DIR)
            ->string($loaderPrefixes['Modules\\'][0])
                ->isEqualTo(MODULES_DIR)
            ->string($loaderPrefixes['Modeles\\'][0])
                ->isEqualTo(MODELES_DIR);
    }
    
    /**
     * Test method for declareRunSteps()
     * 
     * @return void
     */
    public function testDeclareRunSteps()
    {
        $this->assert('test declareRunSteps')
            ->array($runSteps = $this->mock->getRunSteps())
                ->size
                    ->isEqualTo(5)
            ->object($runSteps[0][0])
                ->isInstanceOf('\BFW\Application')
            ->object($runSteps[1][0])
                ->isInstanceOf('\BFW\Application')
            ->object($runSteps[2][0])
                ->isInstanceOf('\BFW\Application')
            ->object($runSteps[3][0])
                ->isInstanceOf('\BFW\Application')
            ->object($runSteps[4][0])
                ->isInstanceOf('\BFW\Application')
            ->string($runSteps[0][1])
                ->isEqualTo('loadMemcached')
            ->string($runSteps[1][1])
                ->isEqualTo('readAllModules')
            ->string($runSteps[2][1])
                ->isEqualTo('loadAllCoreModules')
            ->string($runSteps[3][1])
                ->isEqualTo('loadAllAppModules')
            ->string($runSteps[4][1])
                ->isEqualTo('runCliFile');
    }
    
    /**
     * Test method for notify message during the call to method run()
     * 
     * @return void
     */
    public function testNotifyDuringRun()
    {
        $notifyText = 'apprun_loadMemcached'."\n"
                    .'apprun_readAllModules'."\n"
                    .'apprun_loadAllCoreModules'."\n"
                    .'apprun_loadAllAppModules'."\n"
                    .'apprun_runCliFile'."\n"
                    .'bfw_run_finish'."\n";
        
        $this->assert('test run')
            ->given($app = $this->mock)
            ->given($observer = new MockObserver)
            ->if($this->mock->attach($observer))
            ->output(function() use ($app) {
                $app->run();
            })
                ->isEqualTo($notifyText);
    }
    
    /**
     * Test method for loadMemcached()
     * 
     * @return void
     */
    public function testLoadMemcached()
    {
        $app = $this->mock;
        
        $this->assert('test loadMemcached enabled without class')
            ->if($this->forcedConfig['memcached']['enabled'] = true)
            ->and($this->mock->forceConfig($this->forcedConfig))
            ->then
            ->exception(function() use ($app) {
                $app->run();
            })
                ->hasMessage('Memcached is active but no class is define');
        
        $this->assert('test loadMemcached enabled without class exist')
            ->if($this->forcedConfig['memcached']['class'] = 'TestMemcached')
            ->and($this->mock->forceConfig($this->forcedConfig))
            ->then
            ->exception(function() use ($app) {
                $app->run();
            })
                ->hasMessage('Memcache class TestMemcached not found.');
        
        $this->assert('test loadMemcached enabled without class exist')
            ->if($this->forcedConfig['memcached']['class'] = '\BFW\Memcache\Memcached')
            ->and($this->forcedConfig['memcached']['servers'][0] = [
                    'host' => 'localhost',
                    'port' => 11211
            ])
            ->and($this->mock->forceConfig($this->forcedConfig))
            ->then
            ->variable($this->mock->run())
                ->isNull();
    }
    
    /**
     * Test method for readAllModules() when there is no declared modules.
     * 
     * @return void
     */
    public function testReadAllModulesWithoutModule()
    {
        $this->assert('test readAllModules without modules')
            ->given($this->mock->run())
            ->array($this->mock->getModules()->getLoadTree())
                ->size
                    ->isEqualTo(0);
    }
    
    /**
     * Test method for readAllModules() when there is one module without fail
     * 
     * @return void
     */
    public function testReadAllModulesWithOneGoodModule()
    {
        $this->assert('test readAllModules with one module')
            ->if($this->mock->modulesToAdd['test1'] = (object) [
                'config'       => (object) [],
                'loadInfos'    => (object) []
            ])
            ->and($this->function->scandir = ['.', '..', 'test1'])
            ->and($this->function->realpath = 'test1')
            ->and($this->function->is_dir = true)
            ->then
            ->given($this->mock->run())
            ->array($this->mock->getModules()->getLoadTree())
                ->size
                    ->isGreaterThan(0);
        
        $this->assert('test loadAllAppModules')
            ->boolean($this->mock->getModules()->getModule('test1')->isRun())
                ->isTrue();
        
        $this->assert('test getModule')
            ->object($this->mock->getModule('test1'))
                ->isIdenticalTo($this->mock->getModules()->getModule('test1'));
    }
    
    /**
     * Test method for readAllModules() when there is one module with fail
     * 
     * @return void
     */
    public function testReadAllModulesWithOneBadModule()
    {
        $this->assert('test readAllModules with one module')
            ->if($this->mock->modulesToAdd['test1'] = (object) [
                'config'       => (object) [],
                'loadInfos'    => (object) []
            ])
            ->and($this->function->scandir = ['.', '..', 'test1'])
            ->and($this->function->realpath = 'test1')
            ->and($this->function->is_dir = false)
            ->then
            ->given($this->mock->run())
            ->array($this->mock->getModules()->getLoadTree())
                ->size
                    ->isEqualTo(0);
    }
    
    /**
     * Test method for loadAllCoreModules() when there is no declared modules
     * 
     * @return void
     */
    public function testLoadAllCoreModulesWithoutModule()
    {
        $this->assert('test loadAllCoreModules')
            ->given($app = $this->mock)
            ->given($observer = new MockObserver)
            ->if($this->mock->attach($observer))
            ->output(function() use ($app) {
                $app->run();
            })
                ->notContains('load_module_');
    }
    
    /**
     * Test method for loadAllCoreModules()
     * when there is one module without fail
     * 
     * @return void
     */
    public function testLoadAllCoreModulesWithOneModule()
    {
        $this->assert('test loadAllCoreModules')
            ->given($app = $this->mock)
            ->given($observer = new MockObserver)
            ->if($this->mock->attach($observer))
            ->and($this->forcedConfig['modules']['controller']['name'] = 'test1')
            ->and($this->forcedConfig['modules']['controller']['enabled'] = true)
            ->and($this->mock->forceConfig($this->forcedConfig))
            ->and($this->mock->modulesToAdd['test1'] = (object) [
                'config'       => (object) [],
                'loadInfos'    => (object) []
            ])
            ->and($this->function->scandir = ['.', '..', 'test1'])
            ->and($this->function->realpath = 'test1')
            ->and($this->function->is_dir = true)
            ->then
            ->output(function() use ($app) {
                $app->run();
            })
                ->contains('load_module_test1')
            ->boolean($this->mock->getModules()->getModule('test1')->isRun())
                ->isTrue();
    }
    
    /**
     * Test method for loadAllAppModules()
     * Tested on testReadAllModulesWithOneGoodModule
     * 
     * @return void
     */
    public function testLoadAllAppModules()
    {
        
    }
    
    /**
     * Test method for loadModule()
     * Tested on testReadAllModulesWithOneGoodModule
     * and testLoadAllCoreModulesWithOneModule
     * 
     * @return void
     */
    public function testLoadModule()
    {
        
    }
    
    /**
     * Test method for runCliFile()
     * Tested on test installer scripts
     * 
     * @return void
     */
    public function testRunCliFile()
    {
        
    }
}
