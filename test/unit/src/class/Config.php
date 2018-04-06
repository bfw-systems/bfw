<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Config extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    
    public function beforeTestMethod($testMethod)
    {
        $this->createApp();
        $this->initApp();
        
        if ($testMethod === 'testConstruct') {
            return;
        }
        
        $this->mockGenerator
            ->makeVisible('searchAllConfigFiles')
            ->makeVisible('loadConfigFile')
            ->makeVisible('loadJsonConfigFile')
            ->makeVisible('loadPhpConfigFile')
            ->generate('BFW\Config')
        ;
        $this->mock = new \mock\BFW\Config('test');
    }
    
    public function testConstruct()
    {
        $this->assert('test Config::__construct')
            ->if($config = new \BFW\Config('test'))
            ->then
            
            ->string($config->getConfigDirName())
                ->isEqualTo('test')
            ->string($config->getConfigDir())
                ->isEqualTo(CONFIG_DIR.'test')
        ;
    }
    
    public function testGetConfigDirName()
    {
        $this->assert('test Config::getConfigDirName')
            ->string($this->mock->getConfigDirName())
                ->isEqualTo('test')
        ;
    }
    
    public function testGetConfigDir()
    {
        $this->assert('test Config::getConfigDir')
            ->string($this->mock->getConfigDir())
                ->isEqualTo(CONFIG_DIR.'test')
        ;
    }
    
    public function testGetConfigFiles()
    {
        $this->assert('test Config::getConfigFiles for default value')
            ->array($this->mock->getConfigFiles())
                ->isEmpty()
        ;
        
        $this->assert('test Config::getConfigFiles after adding a config')
            ->if($this->mock->setConfigForFile('test', ['type' => 'unit']))
            ->then
            ->array($this->mock->getConfigFiles())
                ->isEqualTo(['test' => 'test'])
        ;
    }
    
    public function testGetConfig()
    {
        $this->assert('test Config::getConfig for default value')
            ->array($this->mock->getConfig())
                ->isEmpty()
        ;
        
        $this->assert('test Config::getConfig after adding a config')
            ->if($this->mock->setConfigForFile('test', ['type' => 'unit']))
            ->then
            ->array($this->mock->getConfig())
                ->isEqualTo(['test' => ['type' => 'unit']])
        ;
    }
    
    public function testGetAndSetConfigForFile()
    {
        $this->assert('test Config::getConfigForFile for default value')
            ->exception(function() {
                $this->mock->getConfigForFile('test');
            })
                ->hasCode(\BFW\Config::ERR_FILE_NOT_FOUND)
        ;
        
        $this->assert('test Config::setConfigForFile with bad value')
            ->exception(function() {
                $this->mock->setConfigForFile('test', 'unit');
            })
                ->hasCode(\BFW\Config::ERR_VALUE_FORMAT)
        ;
        
        $this->assert('test Config::setConfigForFile with correct value')
            ->object($this->mock->setConfigForFile('test', ['type' => 'unit']))
                ->isIdenticalTo($this->mock)
        ;
        
        $this->assert('test Config::getConfigForFile after value set')
            ->array($this->mock->getConfigForFile('test'))
                ->isEqualTo(['type' => 'unit'])
        ;
    }
    
    public function testLoadFiles()
    {
        $this->assert('test Config::loadFiles')
            ->given($listFilesLoaded = [])
            ->if($this->calling($this->mock)->searchAllConfigFiles = null)
            /**
             * Cannot access protected property mock\BFW\Config::$configFiles
            ->if($this->calling($this->mock)->searchAllConfigFiles = function() {
                $this->configFiles['test'] = CONFIG_DIR.'test/test.php';
            })
            */
            ->and($this->calling($this->mock)->loadConfigFile = function ($fileKey, $filePath) use (&$listFilesLoaded) {
                $listFilesLoaded[$fileKey] = $filePath;
            })
            //So we add a into ConfigFiles with setConfigForFile method.
            ->and($this->mock->setConfigForFile('test', ['type' => 'unit']))
            ->then
            
            ->variable($this->mock->loadFiles())
                ->isNull()
            ->array($listFilesLoaded)
                ->isEqualTo(['test' => 'test'])
        ;
    }
    
    public function testSearchAllConfigFiles()
    {
        $this->assert('test Config::searchAllConfigFiles')
            ->if($this->function->file_exists = function ($path) {
                if ($path === CONFIG_DIR.'test') {
                    return true;
                } elseif ($path === CONFIG_DIR.'test/test_dir') {
                    return true;
                } elseif ($path === '/shared/config/test/test_dir') {
                    return true;
                }
                
                return false;
            })
            ->and($this->function->scandir = function ($path) {
                if ($path === CONFIG_DIR.'test') {
                    return [
                        '.', '..',
                        'test.php', 'test.json',
                        'test_link_file', 'test_link_dir',
                        'test_dir'
                    ];
                } elseif ($path === CONFIG_DIR.'test/test_dir') {
                    return ['.', '..', 'test.json'];
                } elseif ($path === '/shared/config/test/test_dir') {
                    return ['.', '..', 'test.json'];
                }
                
                return ['.', '..'];
            })
            ->and($this->function->is_link = function ($path) {
                if ($path === CONFIG_DIR.'test/test_link_file') {
                    return true;
                } elseif ($path === CONFIG_DIR.'test/test_link_dir') {
                    return true;
                }
                
                return false;
            })
            ->and($this->function->realpath = function ($path) {
                if ($path === CONFIG_DIR.'test/test_link_file') {
                    return '/shared/config/test/test_file';
                } elseif ($path === CONFIG_DIR.'test/test_link_dir') {
                    return '/shared/config/test/test_dir';
                }
                
                return false;
            })
            ->and($this->function->is_file = function ($path) {
                if ($path === CONFIG_DIR.'test/test.php') {
                    return true;
                } elseif ($path === CONFIG_DIR.'test/test.json') {
                    return true;
                } elseif ($path === '/shared/config/test/test_file') {
                    return true;
                } elseif ($path === CONFIG_DIR.'test/test_dir/test.json') {
                    return true;
                } elseif ($path === '/shared/config/test/test_dir/test.json') {
                    return true;
                }
                
                return false;
            })
            ->and($this->function->is_dir = function ($path) {
                if ($path === CONFIG_DIR.'test/test_dir') {
                    return true;
                } elseif ($path === '/shared/config/test/test_dir') {
                    return true;
                }
                
                return false;
            })
            ->then
            
            ->variable($this->invoke($this->mock)->searchAllConfigFiles(CONFIG_DIR.'test'))
                ->isNull()
            ->array($this->mock->getConfigFiles())
                ->isEqualTo([
                    'test.php'                => CONFIG_DIR.'test/test.php',
                    'test.json'               => CONFIG_DIR.'test/test.json',
                    'test_link_file'          => '/shared/config/test/test_file',
                    'test_dir/test.json'      => CONFIG_DIR.'test/test_dir/test.json',
                    'test_link_dir/test.json' => '/shared/config/test/test_dir/test.json'
                ])
        ;
    }
    
    public function testLoadConfigFile()
    {
        $this->assert('test Config::loadConfigFile - prepare')
            ->given($jsonFiles = [])
            ->given($phpFiles = [])
            ->if($this->calling($this->mock)->loadJsonConfigFile = function($fileKey, $filePath) use (&$jsonFiles) {
                $jsonFiles[$fileKey] = $filePath;
            })
            ->and($this->calling($this->mock)->loadPhpConfigFile = function($fileKey, $filePath) use (&$phpFiles) {
                $phpFiles[$fileKey] = $filePath;
            })
            ->then
            
            ->assert('test Config::loadConfigFile - json file')
            ->variable($this->invoke($this->mock)->loadConfigFile(
                'test.json',
                CONFIG_DIR.'test/test.json'
            ))
                ->isNull()
            ->array($jsonFiles)
                ->hasKey('test.json')
            ->string($jsonFiles['test.json'])
                ->isEqualTo(CONFIG_DIR.'test/test.json')
            ->array($phpFiles)
                ->notHasKey('test.json')
            
            ->assert('test Config::loadConfigFile - php file')
            ->variable($this->invoke($this->mock)->loadConfigFile(
                'test.php',
                CONFIG_DIR.'test/test.php'
            ))
                ->isNull()
            ->array($phpFiles)
                ->hasKey('test.php')
            ->string($phpFiles['test.php'])
                ->isEqualTo(CONFIG_DIR.'test/test.php')
            ->array($jsonFiles)
                ->notHasKey('test.php')
            
            ->assert('test Config::loadConfigFile - other extension')
            ->variable($this->invoke($this->mock)->loadConfigFile(
                'test.yml',
                CONFIG_DIR.'test/test.yml'
            ))
                ->isNull()
            ->array($phpFiles)
                ->notHasKey('test.yml')
            ->array($jsonFiles)
                ->notHasKey('test.yml')
        ;
    }
    
    public function testLoadJsonConfigFile()
    {
        $this->assert('test Config::loadJsonConfigFile - correct json')
            ->if($this->function->file_get_contents = '{
                "debug": false,
                "errorRenderFct": {
                    "default": "\\\BFW\\\Core\\\Errors::defaultErrorRender",
                    "cli": "\\\BFW\\\Core\\\Errors::defaultCliErrorRender"
                }
            }')
            ->then
            ->variable($this->invoke($this->mock)->loadJsonConfigFile(
                'test.json',
                CONFIG_DIR.'test/test.json'
            ))
                ->isNull()
            ->object($config = $this->mock->getConfigForFile('test.json'))
                ->isEqualTo((object) [
                    'debug' => false,
                    'errorRenderFct' => (object) [
                        'default' => '\BFW\Core\Errors::defaultErrorRender',
                        'cli' => '\BFW\Core\Errors::defaultCliErrorRender',
                    ]
                ])
        ;
        
        $this->assert('test Config::loadJsonConfigFile - bad json')
            ->if($this->function->file_get_contents = '{
                "debug": false,
            ')
            ->then
            ->exception(function() {
                $this->invoke($this->mock)->loadJsonConfigFile(
                    'test2.json',
                    CONFIG_DIR.'test/test2.json'
                );
            })
                ->hasCode(\BFW\Config::ERR_JSON_PARSE)
        ;
    }
    
    public function testLoadPhpConfigFile()
    {
        $this->assert('test Config::loadPhpConfigFile')
            ->variable($this->invoke($this->mock)->loadPhpConfigFile(
                'test.php',
                __DIR__.'/../../../../install/skeleton/config.php'
            ))
                ->isNull()
            ->array($config = $this->mock->getConfigForFile('test.php'))
                ->isNotEmpty()
        ;
    }
    
    public function testGetValue()
    {
        $this->assert('test Config::getValue with one file - prepare')
            ->given($this->mock->setConfigForFile('test', ['type' => 'unit']))
        ;
        
        $this->assert('test Config::getValue with one file and existing key')
            ->string($this->mock->getValue('type'))
                ->isEqualTo('unit')
        ;
        
        $this->assert('test Config::getValue with one file and not existing key')
            ->exception(function() {
                $this->mock->getValue('unit');
            })
                ->hasCode(\BFW\Config::ERR_KEY_NOT_FOUND)
        ;
        
        $this->assert('test Config::getValue with one indicated file')
            ->string($this->mock->getValue('type', 'test'))
                ->isEqualTo('unit')
        ;
        
        $this->assert('test Config::getValue with two file - prepare')
            ->given($this->mock->setConfigForFile('test2', ['lib' => 'atoum']))
        ;
        
        $this->assert('test Config::getValue with two file and existing key')
            ->string($this->mock->getValue('type', 'test'))
                ->isEqualTo('unit')
            ->string($this->mock->getValue('lib', 'test2'))
                ->isEqualTo('atoum')
        ;
        
        $this->assert('test Config::getValue with two file and not existing key')
            ->exception(function() {
                $this->mock->getValue('unit', 'test2');
            })
                ->hasCode(\BFW\Config::ERR_KEY_NOT_FOUND)
        ;
        
        $this->assert('test Config::getValue with two file but file not indicated')
            ->exception(function() {
                $this->mock->getValue('unit');
            })
                ->hasCode(\BFW\Config::ERR_GETVALUE_FILE_NOT_INDICATED)
        ;
        
        $this->assert('test Config::getValue with two file but file indicated not exist')
            ->exception(function() {
                $this->mock->getValue('unit', 'test3');
            })
                ->hasCode(\BFW\Config::ERR_FILE_NOT_FOUND)
        ;
    }
    
    public function testSetConfigKeyForFile()
    {
        $this->assert('test Config::setConfigKeyForFile with existing file and key')
            ->given($this->mock->setConfigForFile('test', ['type' => 'unit']))
            ->string($this->mock->getValue('type'))
                ->isEqualTo('unit')
            ->object($this->mock->setConfigKeyForFile('test', 'type', 'unit with atoum'))
                ->isIdenticalTo($this->mock)
            ->string($this->mock->getValue('type'))
                ->isEqualTo('unit with atoum')
        ;
        
        $this->assert('test Config::setConfigKeyForFile for new file and key')
            ->array($this->mock->getConfig())
                ->notHasKey('test2')
            ->object($this->mock->setConfigKeyForFile('test2', 'lib', 'atoum'))
                ->isIdenticalTo($this->mock)
            ->array($this->mock->getConfig())
                ->hasKey('test2')
            ->string($this->mock->getValue('lib', 'test2'))
                ->isEqualTo('atoum')
        ;
        
        $this->assert('test Config::setConfigKeyForFile with config value without keys')
            ->if($this->function->file_get_contents = '"test"')
            ->and($this->invoke($this->mock)->loadJsonConfigFile(
                'test3.json',
                CONFIG_DIR.'test/test3.json'
            ))
            ->then
            ->string($this->mock->getConfigForFile('test3.json'))
                ->isEqualTo('test')
            ->exception(function() {
                $this->mock->setConfigKeyForFile('test3.json', 'lib', 'atoum');
            })
                ->hasCode(\BFW\Config::ERR_KEY_NOT_ADDED)
        ;
    }
}