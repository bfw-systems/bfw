<?php

namespace BFW\test\unit;

use \atoum;
use \BFW\test\unit\mocks\ConfigForPhpFile as MockConfigForPhpFile;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Config extends atoum
{
    /**
     * @var $class Class instance
     */
    protected $class;

    /**
     * Call before each test method
     * Define CONFIG_DIR constant
     * Instantiate the class
     * 
     * @param $testMethod string The name of the test method executed
     * 
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        if (
            $testMethod === 'testSearchAllConfigsFilesWithDirectory' || 
            $testMethod === 'testGetConfigExceptions'
        ) {
            return;
        }
        
        define('CONFIG_DIR', '');
        
        $this->class = new \BFW\Config('unit_test');
    }
    
    /**
     * test method for the case where there is no declared config file
     * 
     * @return void
     */
    public function testWhenNoFile()
    {
        $this->assert('test config with no file to found')
            ->if($this->class->loadFiles())
            ->given($config = $this->class)
            ->then
            ->exception(function() use ($config) {
                $config->getConfig('test');
            })->hasMessage('The file  has not been found for config test');
    }
    
    /**
     * test method for the case where there is a json config file
     * 
     * @return void
     */
    public function testWithJsonFile()
    {
        $configJson = '{
            "debug": false,
            "errorRenderFct": {
                "default": "\\\BFW\\\Core\\\Errors::defaultErrorRender",
                "cli": "\\\BFW\\\Core\\\Errors::defaultCliErrorRender"
            }
        }';
        
        $this->assert('test config with a good json file')
            ->if($this->function->file_exists = true)
            ->and($this->function->scandir = ['.', '..', 'test.json'])
            ->and($this->function->is_file = true)
            ->and($this->function->file_get_contents = $configJson)
            ->then
            ->given($this->class->loadFiles())
            ->boolean($this->class->getConfig('debug'))
                ->isFalse()
            ->object($errorRenderFct = $this->class->getConfig('errorRenderFct'))
            ->string($errorRenderFct->default)
                ->isEqualTo('\BFW\Core\Errors::defaultErrorRender')
            ->string($errorRenderFct->cli)
                ->isEqualTo('\BFW\Core\Errors::defaultCliErrorRender');
        
        $this->assert('test config with a bad json file')
            ->if($this->function->file_exists = true)
            ->and($this->function->scandir = ['.', '..', 'test.json'])
            ->and($this->function->is_file = true)
            ->and($this->function->file_get_contents = substr($configJson, 0, -1))
            ->then
            ->exception(function() {
                $config = new \BFW\Config('unit_test');
                $config->loadFiles();
            })->hasMessage('Syntax error');
    }
    
    /**
     * test method for the case where there is a php config file
     * 
     * @return void
     */
    public function testWithPhpFile()
    {
        //$this->function->require : Doesn't work.
        
        $this->assert('test config with a good php file')
            ->if($this->function->file_exists = true)
            ->and($this->function->scandir = ['.', '..', 'test.php'])
            ->and($this->function->is_file = true)
            ->then
            ->if($config = new MockConfigForPhpFile('unit_test'))
            ->and($config->loadFiles())
            ->then
            ->boolean($config->getConfig('debug'))
                ->isFalse()
            ->object($errorRenderFct = $config->getConfig('errorRenderFct'))
            ->string($errorRenderFct->default)
                ->isEqualTo('\BFW\Core\Errors::defaultErrorRender')
            ->string($errorRenderFct->cli)
                ->isEqualTo('\BFW\Core\Errors::defaultCliErrorRender')
            ->variable($config->getConfig('fixNullValue'))
                ->isNull();
    }
    
    /**
     * test method for the case where there is an unsupported config file
     * 
     * @return void
     */
    public function testForUnsupportedFileExt()
    {
        $this->assert('test config with a unsupported file extension')
            ->if($this->function->file_exists = true)
            ->and($this->function->scandir = ['.', '..', 'test.yml'])
            ->and($this->function->is_file = true)
            ->then
            ->if($this->class->loadFiles())
            ->given($config = $this->class)
            ->then
            ->exception(function() use ($config) {
                $config->getConfig('test');
            })->hasMessage('The file  has not been found for config test');
    }
    
    /**
     * test method for searchAllConfigsFiles when the file is a symlink
     * 
     * @return void
     */
    public function testSearchAllConfigsFilesWithLinkedFile()
    {
        $this->assert('test searchAllConfigsFiles for a linked file')
            ->if($this->function->file_exists = true)
            ->and($this->function->scandir = ['.', '..', 'test.json'])
            ->and($this->function->is_file = false)
            ->and($this->function->is_link = true)
            ->and($this->function->realpath = '/tmp/test.json')
            ->and($this->function->file_get_contents = '{"debug": false}')
            ->then
            ->given($this->class->loadFiles())
            ->boolean($this->class->getConfig('debug'))
                ->isFalse();
    }
    
    /**
     * test method for searchAllConfigsFiles when it is a directory
     * 
     * @return void
     */
    public function testSearchAllConfigsFilesWithDirectory()
    {
        $this->assert('test searchAllConfigsFiles for a directory')
            ->given(define('CONFIG_DIR', __DIR__.'/../'))
            ->and($config = new MockConfigForPhpFile('class'))
            ->and($config->loadFiles())
            ->then
            ->boolean($config->getConfig('debug', 'core/Options.php'))
                ->isTrue()
            ->boolean($config->getConfig('debug', 'core/Errors.php'))
                ->isFalse();
    }
    
    /**
     * test method for getConfig() exception messages
     * 
     * @return void
     */
    public function testGetConfigExceptions()
    {
        define('CONFIG_DIR', __DIR__.'/../');
        
        $this->assert('test getConfig exception no file specified')
            ->if($config = new MockConfigForPhpFile('class'))
            ->and($config->loadFiles())
            ->then
            ->exception(function() use ($config) {
                $config->getConfig('debug');
            })->hasMessage(
                'There are many config files. '
                .'Please indicate the file to obtain the config debug'
            );
        
        $this->assert('test getConfig exception unknown key')
            ->if($config = new MockConfigForPhpFile('class'))
            ->and($config->loadFiles())
            ->then
            ->exception(function() use ($config) {
                $config->getConfig('bulton', 'core/Options.php');
            })->hasMessage('The config key bulton has not been found');
    }
}
