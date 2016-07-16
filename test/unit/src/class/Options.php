<?php

namespace BFW\test\unit;
use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Options extends atoum
{
    /**
     * @var $mock : Instance du mock pour la class
     */
    protected $mock;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        $this->mock = new MockOptions([], []);
    }
    
    public function testOptions()
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

/**
 * Mock de la class à tester
 */
class MockOptions extends \BFW\Options
{
    /**
     * Accesseur get
     */
    public function __get($name)
    {
        return $this->$name;
    }
}
