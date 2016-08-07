<?php

namespace BFW\test\unit;

use \atoum;
use \BFW\test\unit\mocks\ConfigForPhpFile as MockConfigForPhpFile;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Config extends atoum
{
    /**
     * @var $class : Instance de la class
     */
    protected $class;

    /**
     * Instanciation de la class avant chaque méthode de test
     */
    public function beforeTestMethod($testMethod)
    {
        if (
            $testMethod === 'testSearchAllConfigsFilesDirFile' || 
            $testMethod === 'testGetConfigExceptions'
        ) {
            return;
        }
        
        define('CONFIG_DIR', '');
        
        $this->class = new \BFW\Config('unit_test');
    }
    
    public function testConfigWithNoFile()
    {
        $this->assert('test config with no file to found')
            ->if($this->class->loadFiles())
            ->given($config = $this->class)
            ->then
            ->exception(function() use ($config) {
                $config->getConfig('test');
            })->hasMessage('The file  not exist for config test');
    }
    
    public function testConfigWithJsonFile()
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
    
    public function testConfigWithPhpFile()
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
                ->isEqualTo('\BFW\Core\Errors::defaultCliErrorRender');
    }
    
    public function testConfigUnsupportedFileExt()
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
            })->hasMessage('The file  not exist for config test');
    }
    
    public function testSearchAllConfigsFilesLinkedFile()
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
    
    public function testSearchAllConfigsFilesDirFile()
    {
        $this->assert('test searchAllConfigsFiles for a dir')
            ->given(define('CONFIG_DIR', __DIR__.'/../'))
            ->and($config = new MockConfigForPhpFile('class'))
            ->and($config->loadFiles())
            ->then
            ->boolean($config->getConfig('debug', 'core/Options.php'))
                ->isTrue()
            ->boolean($config->getConfig('debug', 'core/Errors.php'))
                ->isFalse();
    }
    
    public function testGetConfigExceptions()
    {
        define('CONFIG_DIR', __DIR__.'/../');
        
        $this->assert('test getConfig exception no file specified')
            ->if($config = new MockConfigForPhpFile('class'))
            ->and($config->loadFiles())
            ->then
            ->exception(function() use ($config) {
                $config->getConfig('debug');
            })->hasMessage('Please indicate a file for get config debug');
        
        $this->assert('test getConfig exception unknown key')
            ->if($config = new MockConfigForPhpFile('class'))
            ->and($config->loadFiles())
            ->then
            ->exception(function() use ($config) {
                $config->getConfig('bulton', 'core/Options.php');
            })->hasMessage('The config key bulton not exist in config');
    }
}
