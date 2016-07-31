<?php

namespace BFW\test\unit;
use \atoum;

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
        
        //$this->class = new \BFW\Config;
    }
    
    public function testConfigWithNoFile()
    {
        $this->assert('test config with no file to found')
            ->given($config = new \BFW\Config('unit_test'))
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
            ->given($config = new \BFW\Config('unit_test'))
            ->boolean($config->getConfig('debug'))
                ->isFalse()
            ->object($errorRenderFct = $config->getConfig('errorRenderFct'))
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
                new \BFW\Config('unit_test');
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
            ->given($config = new MockConfigForPhpFile('unit_test'))
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
            ->given($config = new \BFW\Config('unit_test'))
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
            ->given($config = new \BFW\Config('unit_test'))
            ->boolean($config->getConfig('debug'))
                ->isFalse();
    }
    
    public function testSearchAllConfigsFilesDirFile()
    {
        $this->assert('test searchAllConfigsFiles for a dir')
            ->given(define('CONFIG_DIR', __DIR__.'/../'))
            ->given($config = new MockConfigForPhpFile('class'))
            ->boolean($config->getConfig('debug', 'core/Options.php'))
                ->isTrue()
            ->boolean($config->getConfig('debug', 'core/Errors.php'))
                ->isFalse();
    }
    
    public function testGetConfigExceptions()
    {
        define('CONFIG_DIR', __DIR__.'/../');
        
        $this->assert('test getConfig exception no file specified')
            ->given($config = new MockConfigForPhpFile('class'))
            ->exception(function() use ($config) {
                $config->getConfig('debug');
            })->hasMessage('Please indicate a file for get config debug');
        
        $this->assert('test getConfig exception unknown key')
            ->given($config = new MockConfigForPhpFile('class'))
            ->exception(function() use ($config) {
                $config->getConfig('bulton', 'core/Options.php');
            })->hasMessage('The config key bulton not exist in config');
    }
}

/**
 * Atoum doesn't overload protected method.
 */
class MockConfigForPhpFile extends \BFW\Config
{
    protected function loadPhpConfigFile($fileKey, $filePath)
    {
        $debugValue = false;
        if (strpos($filePath, '/class/core/Options.php') !== false) {
            $debugValue = true;
        }
        
        $this->config[$fileKey] = (object) [
            'debug' => $debugValue,
            'errorRenderFct' => (object) [
                'default' => '\BFW\Core\Errors::defaultErrorRender',
                'cli'     => '\BFW\Core\Errors::defaultCliErrorRender'
            ]
        ];
    }
}