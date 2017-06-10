<?php

namespace BFW\Install\test\unit;
use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class ReadDirectory extends atoum
{
    /**
     * @var \BFW\Install\ReadDirectory $class : Tested class instance
     */
    protected $class;
    
    /**
     * @var array $list : Files found list
     */
    protected $list = [];
    
    /**
     * @var int $readdirIndex : Index for readdir mock function
     */
    protected $readdirIndex = -1;

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
        if ($testMethod === 'testConstructor') {
            return;
        }
        
        $this->class = new \BFW\Install\ReadDirectory($this->list);
    }
    
    /**
     * Test method for __construct()
     * 
     * @return void
     */
    public function testConstructor()
    {
        $this->assert('test constructor')
            ->if($this->class = new \BFW\Install\ReadDirectory($this->list))
            ->array($this->list)
                ->size
                    ->isEqualTo(0);
    }
    
    /**
     * Test method for run() with an opendir error
     * 
     * @return void
     */
    public function testRunWithoutDir()
    {
        $this->assert('test run with opendir error.')
            ->if($this->function->opendir = false)
            ->and($this->class->run(''))
            ->array($this->list)
                ->size
                    ->isEqualTo(0);
    }
    
    /**
     * Test method for run()
     * 
     * @return void
     */
    public function testRun()
    {
        $this->assert('test run (call fileAction and dirAction).')
            ->if($this->function->opendir = 'dirPath')
            ->and($this->function->readdir = function() {
                $this->readdirIndex++;
                
                if($this->readdirIndex === 0) {
                    return '.';
                } elseif ($this->readdirIndex === 1) {
                    return '..';
                } elseif ($this->readdirIndex === 2) {
                    return 'test';
                } elseif ($this->readdirIndex === 3) {
                    return 'test2';
                }
                
                return false;
            })
            ->and($this->function->is_dir = function($path) {
                if($path === 'dirPath/test') {
                    return true;
                }
                
                return false;
            })
            ->and($this->function->closedir = true)
            ->then
            
            ->if($this->class->run(''))
            ->array($this->list)
                ->size
                    ->isEqualTo(0);
    }
}
