<?php

namespace BFW\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class CtrlRouterLink extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('obtainCtrlRouterLinkTasks')
            ->makeVisible('runCtrlRouterLink')
        ;
        
        $this->mock = new \mock\BFW\Core\AppSystems\CtrlRouterLink;
        
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        
        $coreSystemList = $this->app->getCoreSystemList();
        unset($coreSystemList['ctrlRouterLink']);
        $this->app->setCoreSystemList($coreSystemList);
        
        $this->initApp();
    }
    
    public function testInit()
    {
        $this->assert('test Core\AppSystems\CtrlRouterLink::isInit before init')
            ->boolean($this->mock->isInit())
                ->isFalse()
        ;
        
        $this->assert('test Core\AppSystems\CtrlRouterLink::init and isInit after')
            ->given($subjectList = \BFW\Application::getInstance()->getSubjectList())
            ->given($appTasks = $subjectList->getSubjectByName('ApplicationTasks'))
            ->given($observer = new \BFW\Test\Helpers\ObserverArray)
            ->and($appTasks->attach($observer))
            ->then
            ->variable($this->mock->init())
                ->isNull()
            ->object($this->mock->getCtrlRouterInfos())
                ->string(get_class($this->mock->getCtrlRouterInfos()))
                    ->contains('class@anonymous')
            ->boolean($this->mock->isInit())
                ->isTrue()
            ->object($tasks = $subjectList->getSubjectByName('ctrlRouterLink'))
                ->isInstanceOf('\BFW\RunTasks')
            ->string($tasks->getNotifyPrefix())
                ->isEqualTo('ctrlRouterLink')
            ->array($tasks->getRunSteps())
                ->size->isEqualTo(3)
            ->array($observer->getActionReceived())
                ->isNotEmpty()
            ->string($observer->getActionReceived()[0])
                ->isEqualTo('bfw_ctrlRouterLink_subject_added')
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\CtrlRouterLink::__invoke')
            ->if($this->mock->init())
            ->then
            ->object($this->mock->__invoke())
                ->isIdenticalTo($this->mock->getCtrlRouterInfos())
        ;
    }
    
    public function testToRun()
    {
        $this->assert('test Core\AppSystems\CtrlRouterLink::toRun')
            ->boolean($this->mock->toRun())
                ->isTrue()
        ;
    }
    
    public function testRunAndIsRun()
    {
        $this->assert('test Core\AppSystems\CtrlRouterLink::isRun before run')
            ->boolean($this->mock->isRun())
                ->isFalse()
        ;
        
        $this->assert('test Core\AppSystems\CtrlRouterLink::run and isRun after')
            ->if($this->mock->init())
            ->and($this->calling($this->mock)->runCtrlRouterLink = null)
            ->variable($this->mock->run())
                ->isNull()
            ->boolean($this->mock->isRun())
                ->isTrue()
            ->mock($this->mock)
                ->call('runCtrlRouterLink')
                    ->once()
        ;
    }
    
    public function testRunCtrlRouterLink()
    {
        $this->assert('test Core\AppSystems\CtrlRouterLink::runCtrlRouterLink')
            ->if($this->mock->init())
            ->then
            
            ->given($observer = new \BFW\Test\Helpers\ObserverArray)
            ->and(
                \BFW\Application::getInstance()
                    ->getSubjectList()
                    ->getSubjectByName('ctrlRouterLink')
                    ->attach($observer)
            )
            ->then
            
            ->variable($this->mock->runCtrlRouterLink())
                ->isNull()
            ->array($observer->getActionReceived())
                ->isEmpty()
        ;
    }
    
    public function testRunCtrlRouterLinkWhenNotCli()
    {
        $this->assert('test Core\AppSystems\CtrlRouterLink::runCtrlRouterLink')
            ->if($this->constant->PHP_SAPI = 'www')
            ->then
            
            ->if($this->mock->init())
            ->then
            
            ->given($observer = new \BFW\Test\Helpers\ObserverArray)
            ->and(
                \BFW\Application::getInstance()
                    ->getSubjectList()
                    ->getSubjectByName('ctrlRouterLink')
                    ->attach($observer)
            )
            ->then
            
            ->variable($this->mock->runCtrlRouterLink())
                ->isNull()
            ->array($actions = $observer->getActionReceived())
                ->isEqualTo([
                    'ctrlRouterLink_start_run_tasks',
                    'ctrlRouterLink_exec_searchRoute',
                    'ctrlRouterLink_run_checkRouteFound',
                    'ctrlRouterLink_done_checkRouteFound',
                    'ctrlRouterLink_exec_execRoute',
                    'ctrlRouterLink_end_run_tasks',
                ])
        ;
    }
    
    public function testCallbackCheckRouteFound()
    {
        $this->assert('test Core\AppSystems\Cli::runCliFile - prepare')
            ->if($this->constant->PHP_SAPI = 'www')
            ->and($this->mock->init())
            ->then
        ;
        
        $this->assert('test Core\AppSystems\Cli::runCliFile if no route found')
            ->if(http_response_code(200))
            ->and($this->mock->getCtrlRouterInfos()->isFound = false)
            ->and($this->mock->run())
            ->then
            
            ->integer(http_response_code())
                ->isEqualTo(404)
        ;
        
        $this->assert('test Core\AppSystems\Cli::runCliFile if route is found')
            ->if(http_response_code(200))
            ->and($this->mock->getCtrlRouterInfos()->isFound = true)
            ->and($this->mock->run())
            ->then
            
            ->integer(http_response_code())
                ->isEqualTo(200)
        ;
    }
}