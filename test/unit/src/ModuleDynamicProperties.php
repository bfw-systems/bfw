<?php

namespace BFW\test\unit;

use atoum;

require_once(__DIR__ . '/../../../vendor/autoload.php');

/**
 * Test for dynamic properties functionality in Module class
 * @engine isolate
 */
class ModuleDynamicProperties extends atoum
{
    use \BFW\Test\Helpers\Application;

    protected $mock;

    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__ . '/../../..');
        $this->createApp();
        $this->initApp();

        $this->mock = new \BFW\Test\Mock\Module('atoum');
    }

    public function testDynamicPropertySet()
    {
        $this->assert('test Module dynamic property setting')
            ->if($this->mock->testProperty = 'test value')
            ->then
            ->string($this->mock->testProperty)
                ->isEqualTo('test value')
        ;
    }

    public function testDynamicPropertyGet()
    {
        $this->assert('test Module dynamic property getting')
            ->if($this->mock->testProperty = 'test value')
            ->then
            ->string($this->mock->testProperty)
                ->isEqualTo('test value')
        ;

        $this->assert('test Module dynamic property getting non-existent')
            ->variable($this->mock->nonExistentProperty)
                ->isNull()
        ;
    }

    public function testDynamicPropertyIsset()
    {
        $this->assert('test Module dynamic property isset')
            ->if($this->mock->testProperty = 'test value')
            ->then
            ->boolean(isset($this->mock->testProperty))
                ->isTrue()
            ->boolean(isset($this->mock->nonExistentProperty))
                ->isFalse()
        ;
    }

    public function testDynamicPropertyUnset()
    {
        $this->assert('test Module dynamic property unset')
            ->if($this->mock->testProperty = 'test value')
            ->and(unset($this->mock->testProperty))
            ->then
            ->boolean(isset($this->mock->testProperty))
                ->isFalse()
            ->variable($this->mock->testProperty)
                ->isNull()
        ;
    }

    public function testDynamicPropertyCallable()
    {
        $this->assert('test Module dynamic callable property')
            ->if($this->mock->testFunction = function($param) {
                return 'called with: ' . $param;
            })
            ->then
            ->string($this->mock->testFunction('test'))
                ->isEqualTo('called with: test')
        ;
    }

    public function testDeclareProperty()
    {
        $this->assert('test Module declareProperty method')
            ->object($this->mock->declareProperty('declaredProp', 'initial value'))
                ->isIdenticalTo($this->mock)
            ->string($this->mock->declaredProp)
                ->isEqualTo('initial value')
        ;
    }

    public function testGetDynamicProperties()
    {
        $this->assert('test Module getDynamicProperties method')
            ->if($this->mock->prop1 = 'value1')
            ->and($this->mock->prop2 = 'value2')
            ->then
            ->array($this->mock->getDynamicProperties())
                ->hasKeys(['prop1', 'prop2'])
                ->containsValues(['value1', 'value2'])
        ;
    }

    public function testObjectPropertyAssignment()
    {
        $this->assert('test Module object property assignment (like fenom example)')
            ->given($fenom = new \stdClass())
            ->if($fenom->name = 'Fenom Template Engine')
            ->and($this->mock->fenom = $fenom)
            ->then
            ->object($this->mock->fenom)
                ->isIdenticalTo($fenom)
            ->string($this->mock->fenom->name)
                ->isEqualTo('Fenom Template Engine')
        ;
    }

    public function testBackwardCompatibilityWithExistingTests()
    {
        $this->assert('test backward compatibility with existing __call functionality')
            ->given($args = [])
            ->if($this->mock->test = function ($test) use (&$args) {
                $args[] = $test;
                return 'atoum';
            })
            ->string($this->mock->test('unit'))
                ->isEqualTo('atoum')
            ->array($args)
                ->isEqualTo(['unit'])
        ;
    }
}