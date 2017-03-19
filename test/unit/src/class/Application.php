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
     * @var $mock : Instance du mock pour la class
     */
    protected $mock;
    
    protected $forcedConfig;

    /**
     * Instanciation de la class avant chaque méthode de test
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
                        $modules::declareModuleConfig($moduleName, $module->config);
                        $modules::declareModuleLoadInfos($moduleName, $module->loadInfos);
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
    
    public function testGetComposerLoader()
    {
        $this->assert('test getComposerLoader')
            ->object($this->mock->getComposerLoader())
                ->isInstanceOf('Composer\Autoload\ClassLoader');
    }
    
    public function testGetConfig()
    {
        $this->assert('test getConfig')
            ->boolean($this->mock->getConfig('debug'))
                ->isFalse()
            ->array($this->mock->getConfig('errorRenderFct'))
                ->isEqualto([
                    'active'  => false,
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
                $app->getConfig('unitTest');
            })
                ->hasMessage('The config key unitTest not exist in config');
    }
    
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
    
    public function testGetRequest()
    {
        $this->assert('test getRequest')
            ->object($request = $this->mock->getRequest())
                ->isInstanceOf('\BFW\Request');
    }
    
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
     * test for initComposerLoader
     * Done by test getComposerLoader and addComposerNamespaces
     */
    public function testInitComposerLoader()
    {
        
    }
    
    /**
     * Test for initConfig
     * Done by getConfig
     * Not in coverrage because mocking.
     */
    public function testInitConfig()
    {
        
    }
    
    /**
     * Test for initRequest
     * Done by getRequest
     */
    public function testInitRequest()
    {
        
    }
    
    public function testInitSessionEnabled()
    {
        $this->assert('test initSession enabled')
            ->string(session_id())
                ->isNotEmpty();
    }
    
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
    
    public function testInitErrors()
    {
        $this->assert('test initErrors')
            ->object($this->mock->getErrors())
                ->isInstanceOf('\BFW\Core\Errors');
    }
    
    public function testInitModules()
    {
        $this->assert('test initModules')
            ->object($this->mock->getModules())
                ->isInstanceOf('\BFW\Modules');
    }
    
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
    
    public function testDeclareRunPhases()
    {
        $this->assert('test declareRunPhases')
            ->array($runPhases = $this->mock->getRunPhases())
                ->size
                    ->isEqualTo(5)
            ->object($runPhases[0][0])
                ->isInstanceOf('\BFW\Application')
            ->object($runPhases[1][0])
                ->isInstanceOf('\BFW\Application')
            ->object($runPhases[2][0])
                ->isInstanceOf('\BFW\Application')
            ->object($runPhases[3][0])
                ->isInstanceOf('\BFW\Application')
            ->object($runPhases[4][0])
                ->isInstanceOf('\BFW\Application')
            ->string($runPhases[0][1])
                ->isEqualTo('loadMemcached')
            ->string($runPhases[1][1])
                ->isEqualTo('readAllModules')
            ->string($runPhases[2][1])
                ->isEqualTo('loadAllCoreModules')
            ->string($runPhases[3][1])
                ->isEqualTo('loadAllAppModules')
            ->string($runPhases[4][1])
                ->isEqualTo('runCliFile');
    }
    
    public function testRunNotify()
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
            ->and($this->forcedConfig['memcached']['server'][0] = [
                    'host' => 'localhost',
                    'port' => 11211
            ])
            ->and($this->mock->forceConfig($this->forcedConfig))
            ->then
            ->variable($this->mock->run())
                ->isNull();
    }
    
    public function testReadAllModulesWithoutModule()
    {
        $this->assert('test readAllModules without modules')
            ->given($this->mock->run())
            ->array($this->mock->getModules()->getLoadTree())
                ->size
                    ->isEqualTo(0);
    }
    
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
     * Tested on testReadAllModulesWithOneGoodModule
     */
    public function testloadAllAppModules()
    {
        
    }
    
    /**
     * Tested on testReadAllModulesWithOneGoodModule and testLoadAllCoreModulesWithOneModule
     */
    public function testLoadModule()
    {
        
    }
    
    /**
     * Tested on test installer scripts
     */
    public function testRunCliFile()
    {
        
    }
}
