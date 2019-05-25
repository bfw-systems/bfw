<?php

namespace BFW\Install\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class ReadDirLoadModule extends atoum
{
    //use \BFW\Test\Helpers\Install\Application;
    
    protected $mock;
    protected $listFiles = [];
    
    public function beforeTestMethod($testMethod)
    {
        //$this->createApp();
        //$this->initApp(); //Need constants
        
        $this->mockGenerator
            ->makeVisible('itemAction')
            ->makeVisible('dirAction')
            ->generate('BFW\Install\ReadDirLoadModule')
        ;
        
        $this->mock = new \mock\BFW\Install\ReadDirLoadModule(
            $this->listFiles
        );
    }
    
    public function testItemAction()
    {
        $this->assert('test Helpers\ReadDirectory::itemAction for parent returned value')
            ->string($this->invoke($this->mock)->itemAction('.', __DIR__))
                ->isEqualTo('continue')
            ->array($this->mock->getList())
                ->isEmpty()
            ->string($this->invoke($this->mock)->itemAction('..', __DIR__))
                ->isEqualTo('continue')
            ->array($this->mock->getList())
                ->isEmpty()
        ;
        
        $this->assert('test Helpers\ReadDirectory::itemAction for a random file')
            ->string($this->invoke($this->mock)->itemAction('Application.php', __DIR__))
                ->isEmpty()
            ->array($this->mock->getList())
                ->isEmpty()
        ;
        
        $this->assert('test Helpers\ReadDirectory::itemAction for the bfwModulesInfos.json file')
            ->string($this->invoke($this->mock)->itemAction('bfwModulesInfos.json', __DIR__))
                ->isEqualTo('break')
            ->array($this->mock->getList())
                ->isEqualTo([
                    0 => __DIR__
                ])
        ;
    }

    public function testDirAction()
    {
        $this->assert('test Helpers\ReadDirectory::dirAction - prepare')
            ->given($pregMathReturn = null)
            ->and($this->function->preg_match = function (...$args) use (&$pregMathReturn) {
                $pregMathReturn = preg_match(...$args);

                return $pregMathReturn;
            })
        ;

        $this->assert('test Helpers\ReadDirectory::dirAction for parent returned value')
            ->given($pregMathReturn = null)
            ->then
            //Take a real directory to read with very few items into it
            ->variable($this->invoke($this->mock)->dirAction(__DIR__.'/../../helpers/Install/'))
                ->isNull()
            ->integer($pregMathReturn)
                ->isEqualTo(0)
        ;

        $this->assert('test Helpers\ReadDirectory::dirAction for parent returned value')
            ->given($pregMathReturn = null)
            ->then
            ->variable($this->invoke($this->mock)->dirAction('vendor/bulton-fr/bfw/test'))
                ->isNull()
            ->integer($pregMathReturn)
                ->isEqualTo(1)
        ;

        $this->assert('test Helpers\ReadDirectory::dirAction for parent returned value')
            ->given($pregMathReturn = null)
            ->then
            ->variable($this->invoke($this->mock)->dirAction('vendor/bulton-fr/bfw/tests'))
                ->isNull()
            ->integer($pregMathReturn)
                ->isEqualTo(1)
        ;
    }
}
