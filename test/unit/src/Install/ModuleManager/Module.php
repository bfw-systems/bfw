<?php

namespace BFW\Install\ModuleManager\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class Module extends atoum
{
    use \BFW\Test\Helpers\Install\Application;
    
    protected $mock;

    protected $fileManager;

    protected $info;
    
    public function beforeTestMethod($testMethod)
    {
        $this->mockGenerator
            ->makeVisible('readModuleInfo')
            ->makeVisible('copyAllConfigFiles')
            ->makeVisible('copyConfigFile')
            ->makeVisible('deleteConfigFiles')
            ->generate('BFW\Install\ModuleManager\Module')
        ;

        $this->setRootDir(__DIR__.'/../../../../..');
        $this->createApp();
        $this->initApp();

        $this->fileManager = new \mock\bultonFr\Utils\Files\FileManager(
            $this->app->getMonolog()->getLogger()
        );

        if ($testMethod === 'testConstructAndDefaultValues') {
            return;
        }

        $this->mock = new \mock\BFW\Install\ModuleManager\Module('hello-world');

        $setFileManager = function ($fileManager) {
            $this->fileManager = $fileManager;
        };
        $setFileManager = $setFileManager->bindTo($this->mock, $this->mock);
        $setFileManager($this->fileManager);

        if ($testMethod !== 'testGetAndSetVendorPath') {
            $this->mock->setVendorPath($this->rootDir.'/vendor/bfw/hello-world');
        }

        if ($testMethod !== 'readModuleInfo') {
            $this->info = (object) [
                'srcPath'       => 'src/',
                'configFiles'   => ['myConfig.php', 'test.json'],
                'configPath'    => 'config/',
                'installScript' => 'install.php'
            ];

            $this->loadInfo();
        }
    }

    protected function loadInfo()
    {
        $setInfo = function ($info) {
            $this->info = $info;
        };
        $setInfo = $setInfo->bindTo($this->mock, $this->mock);

        $setInfo(
            new \mock\BFW\Install\ModuleManager\ModuleInfo($this->info)
        );
    }

    public function testConstructAndDefaultValues()
    {
        $this->assert('test Install\ModuleManager\Module::__constructor')
            ->given($this->mock = new \mock\BFW\Install\ModuleManager\Module('hello-world'))
            ->then
            ->string($this->mock->getName())
                ->isEqualTo('hello-world')
        ;

        $this->assert('test Install\ModuleManager\Module - properties default values')
            ->object($this->mock->getLogger())
                ->isInstanceOf('\Monolog\Logger')
            ->object($this->mock->getFileManager())
                ->isInstanceOf('\bultonFr\Utils\Files\FileManager')
            ->string($this->mock->getVendorPath())
                ->isEmpty()
            ->string($this->mock->getAvailablePath())
                ->isEqualTo(MODULES_AVAILABLE_DIR.'hello-world')
            ->string($this->mock->getEnabledPath())
                ->isEqualTo(MODULES_ENABLED_DIR.'hello-world')
            ->string($this->mock->getConfigPath())
                ->isEqualTo(CONFIG_DIR.'hello-world')
            ->variable($this->mock->getInfo())
                ->isNull()
        ;
    }

    public function testGetAndSetVendorPath()
    {
        $this->assert('test Install\ModuleManager\Module::setVendor')
            ->object($this->mock->setVendorPath('path/to/vendor/bfw/hello-world'))
                ->isIdenticalTo($this->mock)
        ;

        $this->assert('test Install\ModuleManager\Module::getVendor')
            ->string($this->mock->getVendorPath())
                ->isEqualTo('path/to/vendor/bfw/hello-world')
        ;
    }

    public function testDoAdd()
    {
        $this->assert('test Install\ModuleManager\Module::doAdd')
            ->if($this->calling($this->mock)->readModuleInfo = null)
            ->and($this->calling($this->fileManager)->createSymLink = null)
            ->and($this->calling($this->mock)->copyAllConfigFiles = null)
            ->then

            ->variable($this->mock->doAdd())
                ->isNull()
            ->mock($this->mock)
                ->call('readModuleInfo')
                    ->withArguments($this->mock->getVendorPath())
                        ->once()
                ->call('copyAllConfigFiles')
                    ->once()
            ->mock($this->fileManager)
                ->call('createSymLink')
                    ->withArguments(
                        $this->mock->getVendorPath(),
                        $this->mock->getAvailablePath()
                    )
                        ->once()
        ;
    }

    public function testDoEnable()
    {
        $this->assert('test Install\ModuleManager\Module::doEnable')
            ->if($this->calling($this->mock)->readModuleInfo = null)
            ->and($this->calling($this->fileManager)->createSymLink = null)
            ->then

            ->variable($this->mock->doEnable())
                ->isNull()
            ->mock($this->mock)
                ->call('readModuleInfo')
                    ->withArguments($this->mock->getAvailablePath())
                        ->once()
            ->mock($this->fileManager)
                ->call('createSymLink')
                    ->withArguments(
                        $this->mock->getAvailablePath().'/src/',
                        $this->mock->getEnabledPath()
                    )
                        ->once()
        ;
    }

    public function testDoDisable()
    {
        $this->assert('test Install\ModuleManager\Module::doDisable')
            ->if($this->calling($this->mock)->readModuleInfo = null)
            ->and($this->calling($this->fileManager)->removeSymLink = null)
            ->then

            ->variable($this->mock->doDisable())
                ->isNull()
            ->mock($this->mock)
                ->call('readModuleInfo')
                    ->withArguments($this->mock->getAvailablePath())
                        ->once()
            ->mock($this->fileManager)
                ->call('removeSymLink')
                    ->withArguments($this->mock->getEnabledPath())
                        ->once()
        ;
    }

    public function testDoDelete()
    {
        $this->assert('test Install\ModuleManager\Module::doDelete - prepare')
            ->if($this->calling($this->mock)->readModuleInfo = null)
            ->if($this->calling($this->mock)->deleteConfigFiles = null)
            ->and($this->calling($this->fileManager)->removeSymLink = null)
            ->and($this->calling($this->fileManager)->removeRecursiveDirectory = null)
            ->then
            
            ->if($this->function->file_exists = false)
            ->and($this->function->is_link = true)
        ;

        $this->assert('test Install\ModuleManager\Module::doDelete - not enabled - is a link')
            ->if($this->function->file_exists = false)
            ->and($this->function->is_link = true)
            ->then

            ->variable($this->mock->doDelete())
                ->isNull()
            ->mock($this->mock)
                ->call('readModuleInfo')
                    ->withArguments($this->mock->getAvailablePath())
                        ->once()
                ->call('deleteConfigFiles')
                    ->once()
            ->mock($this->fileManager)
                ->call('removeSymLink')
                    ->withArguments($this->mock->getAvailablePath())
                        ->once()
        ;

        $this->assert('test Install\ModuleManager\Module::doDelete - not enabled - not a link')
            ->if($this->function->file_exists = false)
            ->and($this->function->is_link = false)
            ->then
            
            ->variable($this->mock->doDelete())
                ->isNull()
            ->mock($this->mock)
                ->call('readModuleInfo')
                    ->withArguments($this->mock->getAvailablePath())
                        ->once()
                ->call('deleteConfigFiles')
                    ->once()
            ->mock($this->fileManager)
                ->call('removeRecursiveDirectory')
                    ->withArguments($this->mock->getAvailablePath())
                        ->once()
        ;

        $this->assert('test Install\ModuleManager\Module::doDelete - always enabled')
            ->if($this->function->file_exists = true)
            ->and($this->function->is_link = true)
            ->then
            
            ->exception(function () {
                $this->mock->doDelete();
            })
                ->hasCode(\BFW\Install\ModuleManager\Module::EXCEP_DELETE_ENABLED_MODULE)
        ;
    }

    public function testReadModuleInfo()
    {
        $this->assert('test Install\ModuleManager\Module::readModuleInfo')
            ->given($handler = $this->app->getMonolog()->getLogger()->getHandlers()[0])
            ->if(eval('
                namespace BFW {
                    function file_exists(...$args) {
                        return true;
                    }

                    function file_get_contents(...$args) {
                        return \'{
                            "srcPath"       : "src/",
                            "configFiles"   : ["myConfig.php", "test.json"],
                            "configPath"    : "config/",
                            "installScript" : "install.php"
                        }\';
                    }
                }
            ')) //eval, like do atoum internaly with php function mocking
            ->then
            
            ->variable($this->mock->readModuleInfo($this->mock->getAvailablePath()))
                ->isNull()
            ->boolean($handler->hasDebug('Module - Read module info'))
                ->isTrue()
            ->array($allRecords = $handler->getRecords())
            ->array($record = end($allRecords)) //end() need a reference
            ->string($record['message']) //To not search an error on context when it's jsut not the right record.
                ->isEqualTo('Module - Read module info')
            ->array($record['context'])
                ->isEqualTo([
                    'name' => 'hello-world',
                    'path' => $this->mock->getAvailablePath()
                ])
            ->object($info = $this->mock->getInfo())
                ->isInstanceOf('\BFW\Install\ModuleManager\ModuleInfo')
            ->string($info->getSrcPath())
                ->isEqualTo('src/')
            ->array($info->getConfigFiles())
                ->isEqualTo(['myConfig.php', 'test.json'])
            ->string($info->getConfigPath())
                ->isEqualTo('config/')
            ->string($info->getInstallScript())
                ->isEqualTo('install.php')
        ;
    }

    public function testCopyAllConfigFiles()
    {
        $this->assert('test Install\ModuleManager\Module::copyAllConfigFiles - prepare')
            ->if($this->calling($this->mock)->copyConfigFile = null)
            ->and($this->calling($this->fileManager)->createDirectory = null)
            ->given($srcConfigPath = $this->mock->getAvailablePath().'/'.$this->mock->getInfo()->getConfigPath())
            ->given($handler = $this->app->getMonolog()->getLogger()->getHandlers()[0])
        ;

        $this->assert('test Install\ModuleManager\Module::copyAllConfigFiles - with config files')
            ->variable($this->mock->copyAllConfigFiles())
                ->isNull()
            
            ->boolean($handler->hasDebug('Module - Copy config files'))
                ->isTrue()
            ->array($allRecords = $handler->getRecords())
            ->array($record = end($allRecords)) //end() need a reference
            ->string($record['message']) //To not search an error on context when it's jsut not the right record.
                ->isEqualTo('Module - Copy config files')
            ->array($record['context'])
                ->isEqualTo([
                    'name'             => 'hello-world',
                    'configPath'       => $this->mock->getConfigPath(),
                    'sourceConfigPath' => $srcConfigPath,
                    'configFiles'      => $this->mock->getInfo()->getConfigFiles()
                ])
            
            ->mock($this->fileManager)
                ->call('createDirectory')
                    ->withArguments($this->mock->getConfigPath())
                        ->once()
            ->mock($this->mock) //['myConfig.php', 'test.json']
                ->call('copyConfigFile')
                    ->withArguments(
                        $srcConfigPath.'manifest.json',
                        $this->mock->getConfigPath().'/manifest.json'
                    )
                        ->once()
                    ->withArguments(
                        $srcConfigPath.'myConfig.php',
                        $this->mock->getConfigPath().'/myConfig.php'
                    )
                        ->once()
                    ->withArguments(
                        $srcConfigPath.'test.json',
                        $this->mock->getConfigPath().'/test.json'
                    )
                        ->once()
        ;

        $this->assert('test Install\ModuleManager\Module::copyAllConfigFiles - without config files')
            ->given($this->info->configFiles = [])
            ->and($this->loadInfo())
            ->then

            ->variable($this->mock->copyAllConfigFiles())
                ->isNull()
            
            ->boolean($handler->hasDebug('Module - Copy config files'))
                ->isTrue()
            ->array($allRecords = $handler->getRecords())
            ->array($record = end($allRecords)) //end() need a reference
            ->string($record['message']) //To not search an error on context when it's jsut not the right record.
                ->isEqualTo('Module - Copy config files')
            ->array($record['context'])
                ->isEqualTo([
                    'name'             => 'hello-world',
                    'configPath'       => $this->mock->getConfigPath(),
                    'sourceConfigPath' => $srcConfigPath,
                    'configFiles'      => $this->mock->getInfo()->getConfigFiles()
                ])
            
            ->mock($this->fileManager)
                ->call('createDirectory')
                    ->never()
            ->mock($this->mock)
                ->call('copyConfigFile')
                    ->never()
        ;
    }

    public function testCopyConfigFile()
    {
        $this->assert('test Install\ModuleManager\Module::copyConfigFile - success')
            ->if($this->calling($this->fileManager)->copyFile = null)
            ->then

            ->variable($this->mock->copyConfigFile('source/file.php', 'dest/file.php'))
                ->isNull()
            ->mock($this->fileManager)
                ->call('copyFile')
                    ->withArguments('source/file.php', 'dest/file.php')
                        ->once()
        ;

        $this->assert('test Install\ModuleManager\Module::copyConfigFile - exception file exist')
            ->if($this->calling($this->fileManager)->copyFile = function () {
                throw new \Exception(
                    'for unit test',
                    \bultonFr\Utils\Files\FileManager::EXCEP_FILE_EXIST
                );
            })
            ->then

            ->variable($this->mock->copyConfigFile('source/file.php', 'dest/file.php'))
                ->isNull()
            ->mock($this->fileManager)
                ->call('copyFile')
                    ->withArguments('source/file.php', 'dest/file.php')
                        ->once()
        ;

        $this->assert('test Install\ModuleManager\Module::copyConfigFile - exception but not file exist')
            ->if($this->calling($this->fileManager)->copyFile = function () {
                throw new \Exception('for unit test', 9);
            })
            ->then

            ->exception(function () {
                $this->mock->copyConfigFile('source/file.php', 'dest/file.php');
            })
                ->hasCode(9)
        ;
    }

    public function testDeleteConfigFiles()
    {
        $this->assert('test Install\ModuleManager\Module::deleteConfigFiles - prepare')
            ->if($this->calling($this->fileManager)->removeRecursiveDirectory = null)
            ->given($handler = $this->app->getMonolog()->getLogger()->getHandlers()[0])
        ;

        $this->assert('test Install\ModuleManager\Module::deleteConfigFiles - with config files and dir exist')
            ->if($this->function->file_exists = true)
            ->then
            ->variable($this->mock->deleteConfigFiles())
                ->isNull()
            
            ->boolean($handler->hasDebug('Module - Delete config files'))
                ->isTrue()
            ->array($allRecords = $handler->getRecords())
            ->array($record = end($allRecords)) //end() need a reference
            ->string($record['message']) //To not search an error on context when it's jsut not the right record.
                ->isEqualTo('Module - Delete config files')
            ->array($record['context'])
                ->isEqualTo([
                    'name'       => 'hello-world',
                    'configPath' => $this->mock->getConfigPath()
                ])

            ->mock($this->fileManager)
                ->call('removeRecursiveDirectory')
                    ->withArguments($this->mock->getConfigPath())
                        ->once()
        ;

        $this->assert('test Install\ModuleManager\Module::deleteConfigFiles - with config files but without config dir')
            ->if($this->function->file_exists = false)
            ->then
            ->variable($this->mock->deleteConfigFiles())
                ->isNull()
            
            ->boolean($handler->hasDebug('Module - Delete config files'))
                ->isTrue()
            ->array($allRecords = $handler->getRecords())
            ->array($record = end($allRecords)) //end() need a reference
            ->string($record['message']) //To not search an error on context when it's jsut not the right record.
                ->isEqualTo('Module - Delete config files')
            ->array($record['context'])
                ->isEqualTo([
                    'name'       => 'hello-world',
                    'configPath' => $this->mock->getConfigPath()
                ])

            ->mock($this->fileManager)
                ->call('removeRecursiveDirectory')
                    ->never()
        ;
    }

    public function testHasInstallScript()
    {
        $this->assert('test Install\ModuleManager\Module::hasInstallScript - with install script')
            ->boolean($this->mock->hasInstallScript())
                ->isTrue()
        ;

        $this->assert('test Install\ModuleManager\Module::hasInstallScript - without install script')
            ->given($this->info->installScript = '')
            ->and($this->loadInfo())
            ->then

            ->boolean($this->mock->hasInstallScript())
                ->isFalse()
        ;
    }

    public function testRunInstallScript()
    {
        //Cannot test because require_once() cannot be mocked
    }
}
