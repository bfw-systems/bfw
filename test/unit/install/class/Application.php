<?php

namespace BFW\Install\test\unit;

use \atoum;
use \BFW\Install\test\unit\mocks\Application as MockApp;
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
     * @var array BFW config used by unit test
     */
    protected $forcedConfig;

    /**
     * Call before each test method
     * Instantiate the mock
     * 
     * @param $testMethod string The name of the test method executed
     * 
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        $this->forcedConfig = require(__DIR__.'/../../helpers/applicationConfig.php');
        
        //Remove the instance of the latest test
        MockApp::removeInstance();
        
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
                }
            ]
        ];
        
        $this->mock = MockApp::init($options);
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
                    ->isEqualTo(3)
            ->object($runSteps[0][0])
                ->isInstanceOf('\BFW\Application')
            ->object($runSteps[1][0])
                ->isInstanceOf('\BFW\Application')
            ->object($runSteps[2][0])
                ->isInstanceOf('\BFW\Application')
            ->string($runSteps[0][1])
                ->isEqualTo('loadMemcached')
            ->string($runSteps[1][1])
                ->isEqualTo('readAllModules')
            ->string($runSteps[2][1])
                ->isEqualTo('installModules');
    }
    
    /**
     * Test method for runNotify()
     * 
     * @return void
     */
    public function testRunNotify()
    {
        $notifyText = 
            'BfwAppModulesInstall_start_run_tasks'."\n"
            .'BfwAppModulesInstall_run_loadMemcached'."\n"
            .'BfwAppModulesInstall_finish_loadMemcached'."\n"
            .'BfwAppModulesInstall_run_readAllModules'."\n"
            .'BfwAppModulesInstall_finish_readAllModules'."\n"
            .'BfwAppModulesInstall_run_installModules'."\n"
            .'Read all modules to run install script :'."\n"
            .'BfwAppModulesInstall_finish_installModules'."\n"
            .'BfwAppModulesInstall_end_run_tasks'."\n"
            .'bfw_modules_install_finish'."\n"
        ;
        
        $this->assert('test run')
            ->given($app = $this->mock)
            ->given($observer = new MockObserver)
            ->if($this->mock->getSubjectForName('ApplicationTasks')->attach($observer))
            ->output(function() use ($app) {
                $app->run();
            })
                ->isEqualTo($notifyText);
    }
    
    /**
     * Tested by install test
     * 
     * @return void
     */
    public function testInstallModules()
    {
        
    }
    
    /**
     * Tested by install test
     * 
     * @return void
     */
    public function testInstallModule()
    {
        
    }
}
