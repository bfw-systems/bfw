<?php

namespace BFW\test\unit;

use \atoum;
use \BFW\test\unit\mocks\Options as MockOptions;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Options extends atoum
{
    /**
     * @var $mock Mock instance
     */
    protected $mock;

    /**
     * Call before each test method
     * Instantiate the mock
     * 
     * @param $testMethod string The name of the test method executed
     * 
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        $this->mock = new MockOptions([], []);
    }
    
    /**
     * Test method for __construct()
     * 
     * @return void
     */
    public function testConstructor()
    {
        $this->assert('Test constructor, empty array for parameters')
            ->array($this->mock->options)
                ->isEqualTo([]);
        
        //********************
        
        $defaultParameter = [
            'test' => true
        ];
        $parameter = [];
        
        $this->mock = new MockOptions($defaultParameter, $parameter);
        $this->assert('Test constructor, with 1 key default option and empty option')
            ->array($this->mock->options)
                ->isEqualTo($defaultParameter);
        
        //********************
        
        $defaultParameter = [
            'test' => true,
            'test2' => false
        ];
        $parameter = [];
        
        $this->mock = new MockOptions($defaultParameter, $parameter);
        $this->assert('Test constructor, with 2 keys default option and empty option')
            ->array($this->mock->options)
                ->isEqualTo($defaultParameter);
        
        //********************
        
        $defaultParameter = [
            'test' => true,
            'test2' => false
        ];
        $parameter = [];
        
        $this->mock = new MockOptions($defaultParameter, $parameter);
        $this->assert('Test constructor, with 2 keys default parameter and empty option')
            ->array($this->mock->options)
                ->isEqualTo($defaultParameter);
        
        //********************
        
        $defaultParameter = [
            'test' => true
        ];
        $parameter = [
            'test' => false
        ];
        
        $this->mock = new MockOptions($defaultParameter, $parameter);
        $this->assert('Test constructor, with 1 key default option and 1 key option')
            ->array($this->mock->options)
                ->isEqualTo($parameter);
        
        //********************
        
        $defaultParameter = [
            'test' => true,
            'test2' => false
        ];
        $parameter = [
            'test' => false,
            'test2' => true
        ];
        
        $this->mock = new MockOptions($defaultParameter, $parameter);
        $this->assert('Test constructor, with 2keys default option and 2 keys option')
            ->array($this->mock->options)
                ->isEqualTo($parameter);
    }
    
    /**
     * Test method for getOption()
     * 
     * @return void
     */
    public function testGetOption()
    {
        $defaultParameter = [
            'test' => true,
            'test2' => false
        ];
        $parameter = [
            'test' => false,
            'test2' => true
        ];
        
        $this->mock = new MockOptions($defaultParameter, $parameter);
        
        $this->assert('Test getOption : Key exist')
            ->boolean($this->mock->getOption('test'))
                ->isFalse();
        
        $mock = $this->mock;
        $this->assert('Test getOption : Key not exist')
            ->exception(function() use ($mock) {
                $mock->getOption('foo-bar');
            })
            ->hasMessage('Option key foo-bar not exist.');
    }
}
