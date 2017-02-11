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
        
        $options = [
            'forceConfig' => $this->forcedConfig,
            'vendorDir'   => __DIR__.'/../../../../vendor',
            'testOption'  => 'unit test'
        ];
        
        $this->function->scandir = ['.', '..'];
        $this->mock = MockApp::init($options);
    }
    
    public function testDeclareRunPhases()
    {
        $this->assert('test declareRunPhases')
            ->array($runPhases = $this->mock->getRunPhases())
                ->size
                    ->isEqualTo(3)
            ->object($runPhases[0][0])
                ->isInstanceOf('\BFW\Application')
            ->object($runPhases[1][0])
                ->isInstanceOf('\BFW\Application')
            ->object($runPhases[2][0])
                ->isInstanceOf('\BFW\Application')
            ->string($runPhases[0][1])
                ->isEqualTo('loadMemcached')
            ->string($runPhases[1][1])
                ->isEqualTo('readAllModules')
            ->string($runPhases[2][1])
                ->isEqualTo('installModules');
    }
    
    public function testRunNotify()
    {
        $notifyText = 'bfw_modules_install_run_loadMemcached'."\n"
                    .'bfw_modules_install_run_readAllModules'."\n"
                    .'Read all modules to run install script :'."\n"
                    .'bfw_modules_install_run_installModules'."\n"
                    .'bfw_modules_install_finish'."\n"; //Output
        
        $this->assert('test run')
            ->given($app = $this->mock)
            ->given($observer = new MockObserver)
            ->if($this->mock->attach($observer))
            ->output(function() use ($app) {
                $app->run();
            })
                //->hasLength(strlen($notifyText));
                ->isEqualTo($notifyText);
    }
    
    /**
     * Tested by install test
     */
    public function testInstallModules()
    {
        
    }
    
    /**
     * Tested by install test
     */
    public function testInstallModule()
    {
        
    }
}
