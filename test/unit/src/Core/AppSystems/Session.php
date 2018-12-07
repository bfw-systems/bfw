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
        
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        
        //Remove from the list used by initApp() to not run session_start etc
        $appSystemList = $this->app->obtainAppSystemDefaultList();
        unset($appSystemList['session']);
        $this->app->setAppSystemToInstantiate($appSystemList);
        
        if (
            $testMethod === 'testConstructorWithouRunSession' ||
            $testMethod === 'testObtainRunSessionWhenIsFalse'
        ) {
            $this->initApp(false);
        } else {
            $this->initApp(true);
        }
        
        if (
            $testMethod === 'testConstructorWithRunSession' ||
            $testMethod === 'testConstructorWithouRunSession'
        ) {
            return;
        }
        
        $this->mock = new \mock\BFW\Core\AppSystems\Session;
    }
    
    public function testConstructorWithRunSession()
    {
        $this->assert('test Core\AppSystems\Session::init with runSession')
            ->and($this->function->session_set_cookie_params = null)
            ->and($this->function->session_start = null)
            ->then
            
            ->given($this->mock = new \mock\BFW\Core\AppSystems\Session)
            ->then
            ->function('session_set_cookie_params')
                ->wasCalledWithArguments(0)
                    ->once()
            ->function('session_start')
                ->wasCalled()
                    ->once()
        ;
    }
    
    public function testConstructorWithouRunSession()
    {
        $this->assert('test Core\AppSystems\Session::init without runSession')
            ->and($this->function->session_set_cookie_params = null)
            ->and($this->function->session_start = null)
            ->then
            
            ->given($this->mock = new \mock\BFW\Core\AppSystems\Session)
            ->then
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
            ->boolean($this->mock->obtainRunSession())
                ->isFalse()
        ;
    }
    
    public function testObtainRunSessionWhenIsTrue()
    {
        $this->assert('test Core\AppSystems\Session::obtainRunSession when is true')
            ->boolean($this->mock->obtainRunSession())
                ->isTrue()
        ;
    }
}