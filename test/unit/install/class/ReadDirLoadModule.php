<?php

namespace BFW\Install\test\unit;
use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class ReadDirLoadModule extends atoum
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
        
        $this->class = new \BFW\Install\ReadDirLoadModule($this->list);
    }
    
    /**
     * Test method for __construct()
     * 
     * @return void
     */
    public function testConstructor()
    {
        $this->assert('test constructor')
            ->if($this->class = new \BFW\Install\ReadDirLoadModule($this->list))
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
            ->if($this->function->opendir = true)
            ->and($this->function->readdir = function() {
                $this->readdirIndex++;
                
                if($this->readdirIndex === 0) {
                    return '.';
                } elseif ($this->readdirIndex === 1) {
                    return '..';
                } elseif ($this->readdirIndex === 2) {
                    return 'test';
                } elseif ($this->readdirIndex === 3) {
                    return 'bfwModulesInfos.json';
                } elseif ($this->readdirIndex === 4) {
                    return 'test2';
                }
                
                return false;
            })
            ->and($this->function->is_dir = function($path) {
                if($path === 'dirPath/test2') {
                    return true;
                }
                
                return false;
            })
            ->and($this->function->closedir = true)
            ->then
            
            ->if($this->class->run('dirPath'))
            ->array($this->list)
                ->isEqualTo(['dirPath'])
                ->size
                    ->isEqualTo(1);
    }
}
