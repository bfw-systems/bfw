<?php

namespace BFW\Core\AppSystems\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class SubjectList extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        $this->initApp();
        
        if ($testMethod === 'testConstructor') {
            return;
        }
        
        $this->mock = new \mock\BFW\Core\AppSystems\SubjectList;
    }
    
    public function testConstructor()
    {
        $this->assert('test Core\AppSystems\SubjectList::__construct')
            ->given($this->mock = new \mock\BFW\Core\AppSystems\SubjectList)
            ->then
            ->object($this->mock->getSubjectList())
                ->isInstanceOf('\BFW\Core\SubjectList')
        ;
    }
    
    public function testInvoke()
    {
        $this->assert('test Core\AppSystems\SubjectList::__invoke')
            ->object($this->mock->__invoke())
                ->isIdenticalTo($this->mock->getSubjectList())
        ;
    }
    
    public function testToRun()
    {
        $this->assert('test Core\AppSystems\SubjectList::toRun')
            ->boolean($this->mock->toRun())
                ->isFalse()
        ;
    }
}