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
    
    protected function removeExistingSubjects()
    {
        $listInstance = $this->app->getSubjectList();
        $subjects     = $listInstance->getSubjectList();
        
        foreach ($subjects as $subject) {
            $listInstance->removeSubject($subject);
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
            ->array($app->getCoreSystemList())
                ->isNotEmpty()
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
                ->isIdenticalTo($this->app->getCoreSystemList()['config']->getConfig())
        ;
    }
    
    public function testDefineCoreSystemList()
    {
        $this->assert('test Application::defineCoreSystemList')
            ->array($list = $this->app->getCoreSystemList())
                ->size
                    ->isEqualTo(13)
            ->object($list['cli'])
                ->isInstanceOf('\BFW\Core\AppSystems\Cli')
            ->object($list['composerLoader'])
                ->isInstanceOf('\BFW\Core\AppSystems\ComposerLoader')
            ->object($list['config'])
                ->isInstanceOf('\BFW\Core\AppSystems\Config')
            ->object($list['constants'])
                ->isInstanceOf('\BFW\Core\AppSystems\Constants')
            ->object($list['ctrlRouterLink'])
                ->isInstanceOf('\BFW\Core\AppSystems\CtrlRouterLink')
            ->object($list['errors'])
                ->isInstanceOf('\BFW\Core\AppSystems\Errors')
            ->object($list['memcached'])
                ->isInstanceOf('\BFW\Core\AppSystems\Memcached')
            ->object($list['moduleList'])
                ->isInstanceOf('\BFW\Core\AppSystems\ModuleList')
            ->object($list['monolog'])
                ->isInstanceOf('\BFW\Core\AppSystems\Monolog')
            ->object($list['options'])
                ->isInstanceOf('\BFW\Core\AppSystems\Options')
            ->object($list['request'])
                ->isInstanceOf('\BFW\Core\AppSystems\Request')
            ->object($list['session'])
                ->isInstanceOf('\BFW\Core\AppSystems\Session')
            ->object($list['subjectList'])
                ->isInstanceOf('\BFW\Core\AppSystems\SubjectList')
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
    
    public function testInitCoreSystem()
    {
        $this->assert('test Application::initCoreSystem - prepare')
            ->given($coreSystem = new \mock\BFW\Core\AppSystems\AbstractSystem)
            ->and($this->calling($coreSystem)->isInit = true)
            ->then
            ->if($this->app->addToCoreSystemList('mock', $coreSystem))
            ->then
        ;
        
        $this->assert('test Application::initCoreSystem with already init system')
            ->if($this->initApp())
            ->then
            ->mock($coreSystem)
                ->call('init')
                    ->never()
            ->array($this->app->getRunTasks()->getRunSteps())
                ->notHasKey('mock')
        ;
        
        $this->assert('test Application::initCoreSystem with only init system')
            ->if($this->removeExistingSubjects())
            ->then
            ->if($this->calling($coreSystem)->isInit = false)
            ->then
            ->if($this->initApp())
            ->then
            ->mock($coreSystem)
                ->call('init')
                    ->once()
            ->array($this->app->getRunTasks()->getRunSteps())
                ->notHasKey('mock')
        ;
        
        $this->assert('test Application::initCoreSystem with init and run system')
            ->if($this->removeExistingSubjects())
            ->then
            ->if($this->calling($coreSystem)->isInit = false)
            ->and($this->calling($coreSystem)->toRun = true)
            ->then
            ->if($this->initApp())
            ->then
            ->mock($coreSystem)
                ->call('init')
                    ->once()
            ->array($this->app->getRunTasks()->getRunSteps())
                ->hasKey('mock')
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