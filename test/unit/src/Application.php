<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Application extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../..');
        
        if ($testMethod === 'testConstructAndGetInstance') {
            return;
        }
        
        $this->createApp();
        
        $testWithAppInitialized = [
            'testCall',
            'testRun'
        ];
        
        if (in_array($testMethod, $testWithAppInitialized)) {
            $this->initApp();
        }
    }
    
    /**
     * Test method for __constructor() and getInstance()
     * 
     * @return void
     */
    public function testConstructAndGetInstance()
    {
        $this->assert('test Application::__construct and Application::getInstance')
            ->object($app = \BFW\Test\Mock\Application::getInstance())
                ->isInstanceOf('\BFW\Application')
            ->object(\BFW\Test\Mock\Application::getInstance())
                ->isIdenticalTo($app)
            ->string(ini_get('default_charset'))
                ->isEqualTo('UTF-8')
            ;
        ;
    }
    
    public function testCall()
    {
        $this->assert('test Application::__call when unknown method')
            ->exception(function() {
                $this->app->setConfig(null);
            })
                ->hasCode(\BFW\Application::ERR_CALL_UNKNOWN_METHOD)
        ;
        
        $this->assert('test Application::__call when unknown property')
            ->exception(function() {
                $this->app->getAtoum();
            })
                ->hasCode(\BFW\Application::ERR_CALL_UNKNOWN_PROPERTY)
        ;
        
        $this->assert('test Application::__call when known method')
            ->object($this->app->getConfig())
                ->isInstanceOf('\BFW\Config')
                ->isIdenticalTo($this->app->getAppSystemList()['config']->getConfig())
        ;
    }
    
    public function testObtainAppSystemList()
    {
        $this->assert('test Application::obtainAppSystemList')
            ->array($list = $this->app->obtainParentAppSystemList())
                ->size
                    ->isEqualTo(12)
            ->string($list['composerLoader'])
                ->isEqualTo('\BFW\Core\AppSystems\ComposerLoader')
            ->string($list['config'])
                ->isEqualTo('\BFW\Core\AppSystems\Config')
            ->string($list['constants'])
                ->isEqualTo('\BFW\Core\AppSystems\Constants')
            ->string($list['ctrlRouterLink'])
                ->isEqualTo('\BFW\Core\AppSystems\CtrlRouterLink')
            ->string($list['errors'])
                ->isEqualTo('\BFW\Core\AppSystems\Errors')
            ->string($list['memcached'])
                ->isEqualTo('\BFW\Core\AppSystems\Memcached')
            ->string($list['moduleList'])
                ->isEqualTo('\BFW\Core\AppSystems\ModuleList')
            ->string($list['monolog'])
                ->isEqualTo('\BFW\Core\AppSystems\Monolog')
            ->string($list['options'])
                ->isEqualTo('\BFW\Core\AppSystems\Options')
            ->string($list['request'])
                ->isEqualTo('\BFW\Core\AppSystems\Request')
            ->string($list['session'])
                ->isEqualTo('\BFW\Core\AppSystems\Session')
            ->string($list['subjectList'])
                ->isEqualTo('\BFW\Core\AppSystems\SubjectList')
        ;
    }
    
    public function testInitSystems()
    {
        $this->assert('test Application::initSystems')
            ->if($this->initApp())
            ->then
            ->array($this->app->getDeclaredOptions())
                ->isNotEmpty()
            ->object($runTasks = $this->app->getRunTasks())
                ->isInstanceOf('\BFW\RunTasks')
            ->string($runTasks->getNotifyPrefix())
                ->isEqualTo('BfwApp')
            ->object($this->app->getSubjectList()->getSubjectByName('ApplicationTasks'))
                ->isIdenticalTo($runTasks)
            ->given($logger = $this->app->getMonolog()->getLogger())
            ->given($handler = $logger->getHandlers()[0])
            ->boolean($handler->hasDebugRecords())
                ->isTrue()
            ->array($records = $handler->getRecords())
                ->size
                    ->isEqualTo(4)
            ->string($records[0]['message'])
                ->isEqualTo('Currently during the initialization framework step.')
            ->string($records[3]['message'])
                ->isEqualTo('Framework initializing done.')
        ;
        
        /**
         * [2018-07-29 19:32:59] bfw.DEBUG: Currently during the initialization framework step. [] []
         * [2018-07-29 19:32:59] bfw.DEBUG: RunTask notify {"prefix":"BfwApp","action":"bfw_ctrlRouterLink_subject_added"} []
         * [2018-07-29 19:32:59] bfw.DEBUG: Subject notify event {"action":"bfw_ctrlRouterLink_subject_added"} []
         * [2018-07-29 19:32:59] bfw.DEBUG: Framework initializing done. [] []
         */
    }
    
    public function testInitAppSystemWithNonExistingClass()
    {
        $this->assert('test Application::initAppSystem - prepare')
            ->given($list = $this->app->obtainAppSystemDefaultList())
            ->and($this->app->setAppSystemToInstantiate($list))
        ;
        
        $this->assert('test Application::initAppSystem with an unknown class')
            ->if($this->app->addToAppSystemToInstantiate('mock', 'unknownClass'))
            ->then
            ->exception(function() {
                $this->initApp();
            })
                ->hasCode(\BFW\Application::ERR_APP_SYSTEM_CLASS_NOT_EXIST)
        ;
    }
    
    public function testInitAppSystemWithClassNotImplementInterface()
    {
        $this->assert('test Application::initAppSystem - prepare')
            ->given($list = $this->app->obtainAppSystemDefaultList())
            ->and($this->app->setAppSystemToInstantiate($list))
        ;
        
        $this->assert('test Application::initAppSystem with a class which not implement the interface')
            ->if($this->app->addToAppSystemToInstantiate('mock', '\BFW\Helpers\Dates'))
            ->then
            ->exception(function() {
                $this->initApp();
            })
                ->hasCode(\BFW\Application::ERR_APP_SYSTEM_NOT_IMPLEMENT_INTERFACE)
        ;
    }
    
    public function testInitAppSystemWithoutRun()
    {
        $this->assert('test Application::initAppSystem - prepare')
            ->given($list = $this->app->obtainAppSystemDefaultList())
            ->and($this->app->setAppSystemToInstantiate($list))
        ;
        
        $this->assert('test Application::initAppSystem without run system')
            ->if($this->app->addToAppSystemToInstantiate('mock', '\mock\BFW\Core\AppSystems\AbstractSystem'))
            ->then
            ->if($this->initApp())
            ->then
            ->array($this->app->getAppSystemList())
                ->hasKey('mock')
            ->object($this->app->getAppSystemList()['mock'])
                ->isInstanceOf('\mock\BFW\Core\AppSystems\AbstractSystem')
            ->array($this->app->getRunTasks()->getRunSteps())
                ->notHasKey('mock')
        ;
    }
    
    public function testInitAppSystemWithRun()
    {
        $this->assert('test Application::initAppSystem - prepare')
            ->given($list = $this->app->obtainAppSystemDefaultList())
            ->and($this->app->setAppSystemToInstantiate($list))
        ;
        
        $this->assert('test Application::initAppSystem with run system')
            ->if($this->app->addToAppSystemToInstantiate('mock_moduleList', '\mock\BFW\Core\AppSystems\ModuleList'))
            ->then
            ->if($this->initApp())
            ->then
            ->array($this->app->getAppSystemList())
                ->hasKey('mock_moduleList')
            ->object($this->app->getAppSystemList()['mock_moduleList'])
                ->isInstanceOf('\mock\BFW\Core\AppSystems\ModuleList')
            ->array($this->app->getRunTasks()->getRunSteps())
                ->hasKey('mock_moduleList')
        ;
    }
    
    public function testRun()
    {
        $this->assert('test Application::run')
            ->given($runTasks = new \mock\BFW\RunTasks([], 'BfwApp'))
            ->if($this->app->setRunTasks($runTasks))
            ->then
            
            ->variable($this->app->run())
                ->isNull()
            ->mock($runTasks)
                ->call('run')
                    ->once()
                ->call('sendNotify')
                    ->withArguments('bfw_run_done')
                        ->once()
            ->then
            
            ->given(
                $records = $this
                    ->app
                    ->getMonolog()
                    ->getLogger()
                    ->getHandlers()[0]
                    ->getRecords()
            )
            ->string($records[4]['message'])
                ->isEqualTo('running framework')
            ->string($records[8]['context']['action'])
                ->isEqualTo('bfw_run_done')
        ;
        
        /**
         * [2018-07-29 20:00:49] bfw.DEBUG: Currently during the initialization framework step. [] []
         * [2018-07-29 20:00:49] bfw.DEBUG: RunTask notify {"prefix":"BfwApp","action":"bfw_ctrlRouterLink_subject_added"} []
         * [2018-07-29 20:00:49] bfw.DEBUG: Subject notify event {"action":"bfw_ctrlRouterLink_subject_added"} []
         * [2018-07-29 20:00:49] bfw.DEBUG: Framework initializing done. [] []
         * [2018-07-29 20:00:49] bfw.DEBUG: running framework [] []
         * [2018-07-29 20:00:49] bfw.DEBUG: Subject notify event {"action":"BfwApp_start_run_tasks"} []
         * [2018-07-29 20:00:49] bfw.DEBUG: Subject notify event {"action":"BfwApp_end_run_tasks"} []
         * [2018-07-29 20:00:49] bfw.DEBUG: RunTask notify {"prefix":"BfwApp","action":"bfw_run_done"} []
         * [2018-07-29 20:00:49] bfw.DEBUG: Subject notify event {"action":"bfw_run_done"} []
         */
    }
}