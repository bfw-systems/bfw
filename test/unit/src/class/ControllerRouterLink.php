<?php

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class ControllerRouterLink extends atoum
{
    /**
     * @var $class Class instance
     */
    protected $class;

    /**
     * Call before each test method
     * Instantiate the class
     * 
     * @param $testMethod string The name of the test method executed
     * 
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        if($testMethod === 'testGetInstance') {
            return;
        }
        
        $this->class = \BFW\ControllerRouterLink::getInstance();
    }
    
    /**
     * Test method for getInstance()
     * 
     * @return void
     */
    public function testGetInstance()
    {
        $this->assert('test getInstance : create new instance')
            ->given($firstInstance = \BFW\ControllerRouterLink::getInstance())
            ->object($firstInstance)
                ->isInstanceOf('\BFW\ControllerRouterLink');
        
        $this->assert('test getInstance : get last instance')
            ->given($getInstance = \BFW\ControllerRouterLink::getInstance())
            ->object($getInstance)
                ->isInstanceOf('\BFW\ControllerRouterLink')
                ->isIdenticalTo($firstInstance);
    }
    
    /**
     * Test method for getTarget() and setTarget()
     * 
     * @return void
     */
    public function testGetTargetAndSetTarget()
    {
        $this->assert('test getTarget with default value')
            ->variable($this->class->getTarget())
                ->isNull();
        
        $this->assert('test setTarget')
            ->object($this->class->setTarget('unit_test'))
                ->isIdenticalTo($this->class);
        
        $this->assert('test getTarget with new value')
            ->string($this->class->getTarget())
                ->isEqualTo('unit_test');
    }
    
    /**
     * Test method for getDatas() and setDatas()
     * 
     * @return void
     */
    public function testGetDatasAndSetDatas()
    {
        $this->assert('test getDatas with default value')
            ->variable($this->class->getDatas())
                ->isNull();
        
        $this->assert('test setTarget')
            ->object($this->class->setDatas(['test' => 'unit']))
                ->isIdenticalTo($this->class);
        
        $this->assert('test getTarget with new value')
            ->array($this->class->getDatas())
                ->isEqualTo(['test' => 'unit']);
    }
}
