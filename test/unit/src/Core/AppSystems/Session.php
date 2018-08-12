<?php

namespace BFW\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Session extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('obtainRunSession')
        ;
        
        $this->mock = new \mock\BFW\Core\AppSystems\Session;
        
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        
        //Remove from the list used by initApp() to not run session_start etc
        $coreSystemList = $this->app->getCoreSystemList();
        unset($coreSystemList['session']);
        $this->app->setCoreSystemList($coreSystemList);
        
        if (
            $testMethod === 'testObtainRunSessionWhenIsFalse' ||
            $testMethod === 'testObtainRunSessionWhenIsTrue'
        ) {
            return;
        }
        
        $this->initApp(true);
    }
    
    public function testInitWithRunSession()
    {
        $this->assert('test Core\AppSystems\Session::init with runSession')
            ->if($this->calling($this->mock)->obtainRunSession = true)
            ->and($this->function->session_set_cookie_params = null)
            ->and($this->function->session_start = null)
            ->then
            
            ->boolean($this->mock->isInit())
                ->isFalse()
            ->variable($this->mock->init())
                ->isNull()
            ->boolean($this->mock->isInit())
                ->isTrue()
            ->function('session_set_cookie_params')
                ->wasCalledWithArguments(0)
                    ->once()
            ->function('session_start')
                ->wasCalled()
                    ->once()
        ;
    }
    
    public function testInitWithouRunSession()
    {
        $this->assert('test Core\AppSystems\Session::init without runSession')
            ->if($this->calling($this->mock)->obtainRunSession = false)
            ->and($this->function->session_set_cookie_params = null)
            ->and($this->function->session_start = null)
            ->then
            
            ->boolean($this->mock->isInit())
                ->isFalse()
            ->variable($this->mock->init())
                ->isNull()
            ->boolean($this->mock->isInit())
                ->isTrue()
            ->function('session_set_cookie_params')
                ->wasCalled()
                    ->never()
            ->function('session_start')
                ->wasCalled()
                    ->never()
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\Session::__invoke')
            ->if($this->mock->init())
            ->then
            ->variable($this->mock->__invoke())
                ->isNull()
        ;
    }
    
    public function testToRun()
    {
        $this->assert('test Core\AppSystems\Session::toRun')
            ->boolean($this->mock->toRun())
                ->isFalse()
        ;
    }
    
    public function testObtainRunSessionWhenIsFalse()
    {
        $this->assert('test Core\AppSystems\Session::obtainRunSession when is false')
            ->if($this->initApp(false))
            ->then
            ->boolean($this->mock->obtainRunSession())
                ->isFalse()
        ;
    }
    
    public function testObtainRunSessionWhenIsTrue()
    {
        $this->assert('test Core\AppSystems\Session::obtainRunSession when is true')
            ->if($this->initApp(true))
            ->then
            ->boolean($this->mock->obtainRunSession())
                ->isTrue()
        ;
    }
}