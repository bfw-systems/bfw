<?php

namespace BFW\Core\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class ErrorsDisplay extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../../..');
        //$this->createApp();
        //$this->initApp();
        
        $this->mock = new \mock\BFW\Core\ErrorsDisplay;
    }
    
    public function testDefaultCliErrorRender()
    {
        //Can not be tested because call to exit
    }
    
    public function testDefaultErrorRender()
    {
        //Can not be tested because call to exit
    }
}
