<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Options extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../../..');
        $this->createApp();
        $this->initApp();
        
        if ($testMethod == 'testConstruct') {
            return;
        }
    }
    
    public function testConstructAndGetOptions()
    {
        $this->assert('test Options::__construct only with default options')
            ->if($mock = new \BFW\Options(['lib' => 'atoum'], []))
            ->then
            ->array($mock->getOptions())
                ->isEqualTo(['lib' => 'atoum'])
        ;
        
        $this->assert('test Options::__construct only with personal options')
            ->if($mock = new \BFW\Options([], ['lib' => 'atoum']))
            ->then
            ->array($mock->getOptions())
                ->isEqualTo(['lib' => 'atoum'])
        ;
        
        $this->assert('test Options::__construct with default and personal options')
            ->if($mock = new \BFW\Options(
                [
                    'testLib' => null,
                    'fwkName' => 'bfw'
                ],
                [
                    'testLib' => 'atoum',
                    'inTest'  => true
                ]
            ))
            ->then
            ->array($mock->getOptions())
                ->isEqualTo([
                    'testLib'  => 'atoum',
                    'fwkName'  => 'bfw',
                    'inTest'  => true
                ])
        ;
    }
    
    public function testGetValue()
    {
        $this->assert('test Options::getValue')
            ->if($this->mock = new \BFW\Options(
                [
                    'testLib' => null,
                    'fwkName' => 'bfw'
                ],
                [
                    'testLib' => 'atoum',
                    'inTest'  => true
                ]
            ))
            ->then
            ->string($this->mock->getValue('testLib'))
                ->isEqualTo('atoum')
            ->string($this->mock->getValue('fwkName'))
                ->isEqualTo('bfw')
            ->boolean($this->mock->getValue('inTest'))
                ->isTrue()
            ->exception(function() {
                $this->mock->getValue('hello-world');
            })
                ->hasCode(\BFW\Options::ERR_KEY_NOT_EXIST)
        ;
    }
}