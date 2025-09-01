<?php

namespace BFW\test\unit;

use \atoum;

require_once(__DIR__.'/../../../vendor/autoload.php');

/**
 * @engine isolate
 */
class EncryptedConfig extends atoum
{
    use \BFW\Test\Helpers\Application;
    
    protected $mock;
    protected $testConfigDir;
    
    public function beforeTestMethod($testMethod)
    {
        $this->setRootDir(__DIR__.'/../../..');
        $this->createApp();
        $this->initApp();
        
        // Create a temporary config directory for testing
        $this->testConfigDir = sys_get_temp_dir() . '/bfw_encrypted_config_test_' . uniqid();
        mkdir($this->testConfigDir, 0755, true);
        
        $this->mockGenerator
            ->makeVisible('loadConfigFile')
            ->makeVisible('loadEncryptedConfigFile')
            ->makeVisible('decryptData')
            ->makeVisible('getOriginalFileExtension')
            ->makeVisible('parseDecryptedData')
            ->makeVisible('evaluatePhpConfig')
            ->makeVisible('deriveEncryptionKey')
            ->makeVisible('getApplicationSecret')
            ->makeVisible('getSystemEntropy')
            ->generate('BFW\EncryptedConfig')
        ;
        
        if ($testMethod == 'testConstruct') {
            return;
        }
        
        $this->mock = new \mock\BFW\EncryptedConfig('test', 'testModule');
    }
    
    public function afterTestMethod($testMethod)
    {
        // Clean up test directory
        if ($this->testConfigDir && is_dir($this->testConfigDir)) {
            $this->removeDirectory($this->testConfigDir);
        }
    }
    
    protected function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
    
    public function testConstruct()
    {
        $this->assert('test EncryptedConfig::__construct with OpenSSL available')
            ->object($this->mock = new \mock\BFW\EncryptedConfig('test', 'testModule'))
                ->isInstanceOf('\BFW\EncryptedConfig')
                ->isInstanceOf('\BFW\Config')
        ;
        
        $this->assert('test EncryptedConfig::__construct without OpenSSL extension')
            ->if($this->function->extension_loaded = false)
            ->then
            ->exception(function() {
                new \BFW\EncryptedConfig('test', 'testModule');
            })
                ->hasCode(\BFW\EncryptedConfig::ERR_MISSING_CRYPTO_EXTENSION)
        ;
    }
    
    public function testEncryptAndDecryptConfigData()
    {
        $this->assert('test EncryptedConfig::encryptConfigData and decryptData')
            ->given($testData = '{"api_key": "secret123", "password": "mypass"}')
            ->given($originalExtension = 'json')
            ->string($encrypted = $this->mock->encryptConfigData($testData, $originalExtension))
                ->isNotEmpty()
                ->isNotEqualTo($testData)
            ->string($decrypted = $this->invoke($this->mock)->decryptData($encrypted))
                ->contains($testData) // Contains because of metadata
        ;
    }
    
    public function testGetOriginalFileExtension()
    {
        $this->assert('test EncryptedConfig::getOriginalFileExtension')
            ->given($testData = '{"test": "data"}')
            ->given($originalExtension = 'json')
            ->given($encrypted = $this->mock->encryptConfigData($testData, $originalExtension))
            ->given($decrypted = $this->invoke($this->mock)->decryptData($encrypted))
            ->string($extension = $this->invoke($this->mock)->getOriginalFileExtension($decrypted))
                ->isEqualTo('json')
            ->string($decrypted) // Should be stripped of metadata
                ->isEqualTo($testData)
        ;
    }
    
    public function testParseDecryptedData()
    {
        $this->assert('test EncryptedConfig::parseDecryptedData with JSON')
            ->given($jsonData = '{"key": "value", "number": 42}')
            ->object($parsed = $this->invoke($this->mock)->parseDecryptedData($jsonData, 'json'))
                ->isEqualTo((object) ['key' => 'value', 'number' => 42])
        ;
        
        $this->assert('test EncryptedConfig::parseDecryptedData with PHP')
            ->given($phpData = 'return ["key" => "value", "number" => 42];')
            ->array($parsed = $this->invoke($this->mock)->parseDecryptedData($phpData, 'php'))
                ->isEqualTo(['key' => 'value', 'number' => 42])
        ;
        
        $this->assert('test EncryptedConfig::parseDecryptedData with unknown format')
            ->given($textData = 'plain text data')
            ->string($parsed = $this->invoke($this->mock)->parseDecryptedData($textData, 'txt'))
                ->isEqualTo('plain text data')
        ;
    }
    
    public function testDeriveEncryptionKey()
    {
        $this->assert('test EncryptedConfig::deriveEncryptionKey')
            ->string($key1 = $this->invoke($this->mock)->deriveEncryptionKey())
                ->hasLength(32) // 256-bit key
        ;
        
        $this->assert('test different modules generate different keys')
            ->given($mock2 = new \mock\BFW\EncryptedConfig('test', 'differentModule'))
            ->string($key2 = $this->invoke($mock2)->deriveEncryptionKey())
                ->hasLength(32)
                ->isNotEqualTo($key1)
        ;
    }
    
    public function testGetApplicationSecret()
    {
        $this->assert('test EncryptedConfig::getApplicationSecret')
            ->string($secret = $this->invoke($this->mock)->getApplicationSecret())
                ->isNotEmpty()
        ;
        
        $this->assert('test with environment variable')
            ->if($this->function->getenv = 'test-secret-from-env')
            ->string($secret = $this->invoke($this->mock)->getApplicationSecret())
                ->isEqualTo('test-secret-from-env')
        ;
    }
    
    public function testGetSystemEntropy()
    {
        $this->assert('test EncryptedConfig::getSystemEntropy')
            ->string($entropy = $this->invoke($this->mock)->getSystemEntropy())
                ->isNotEmpty()
        ;
    }
    
    public function testLoadEncryptedConfigFile()
    {
        $this->assert('test EncryptedConfig::loadEncryptedConfigFile')
            ->given($testConfig = ['api_key' => 'secret123', 'debug' => true])
            ->given($jsonData = json_encode($testConfig))
            ->given($encrypted = $this->mock->encryptConfigData($jsonData, 'json'))
            ->given($testFile = $this->testConfigDir . '/test.enc')
            ->if(file_put_contents($testFile, $encrypted))
            ->then
            ->variable($this->invoke($this->mock)->loadEncryptedConfigFile('test.enc', $testFile))
                ->isNull()
            ->array($config = $this->mock->getConfig())
                ->hasKey('test.enc')
            ->object($config['test.enc'])
                ->isEqualTo((object) $testConfig)
        ;
    }
    
    public function testSaveEncryptedConfig()
    {
        $this->assert('test EncryptedConfig::saveEncryptedConfig')
            ->given($testConfig = ['secret' => 'very-secret-data', 'tokens' => ['api' => 'abc123']])
            ->given($configDir = $this->testConfigDir . '/module1/private')
            ->given($mock = new \BFW\EncryptedConfig('module1/private', 'module1'))
            ->given($this->calling($mock)->getConfigDir = $configDir)
            ->if(mkdir($configDir, 0755, true))
            ->boolean($result = $mock->saveEncryptedConfig('secrets', $testConfig, 'json'))
                ->isTrue()
            ->boolean(file_exists($configDir . '/secrets.enc'))
                ->isTrue()
        ;
        
        $this->assert('test loading the saved encrypted config')
            ->given($mock2 = new \BFW\EncryptedConfig('module1/private', 'module1'))
            ->given($this->calling($mock2)->getConfigDir = $configDir)
            ->variable($mock2->loadFiles())
                ->isNull()
            ->array($config = $mock2->getConfig())
                ->hasKey('secrets.enc')
            ->object($config['secrets.enc'])
                ->isEqualTo((object) $testConfig)
        ;
    }
    
    public function testEncryptionErrors()
    {
        $this->assert('test decryption with invalid base64')
            ->exception(function() {
                $this->invoke($this->mock)->decryptData('invalid-base64-data!@#');
            })
                ->hasCode(\BFW\EncryptedConfig::ERR_INVALID_ENCRYPTED_FILE)
        ;
        
        $this->assert('test decryption with file too short')
            ->exception(function() {
                $this->invoke($this->mock)->decryptData(base64_encode('short'));
            })
                ->hasCode(\BFW\EncryptedConfig::ERR_INVALID_ENCRYPTED_FILE)
        ;
        
        $this->assert('test decryption with wrong key/corrupted data')
            ->given($validEncrypted = $this->mock->encryptConfigData('{"test": "data"}', 'json'))
            ->given($mock2 = new \mock\BFW\EncryptedConfig('test', 'differentModule'))
            ->exception(function() use ($mock2, $validEncrypted) {
                $this->invoke($mock2)->decryptData($validEncrypted);
            })
                ->hasCode(\BFW\EncryptedConfig::ERR_DECRYPTION_FAILED)
        ;
    }
    
    public function testLoadConfigFileDispatch()
    {
        $this->assert('test EncryptedConfig::loadConfigFile dispatches to encrypted handler')
            ->given($testFile = $this->testConfigDir . '/test.enc')
            ->given($this->calling($this->mock)->loadEncryptedConfigFile = null)
            ->if(touch($testFile))
            ->then
            ->variable($this->invoke($this->mock)->loadConfigFile('test.enc', $testFile))
                ->isNull()
            ->mock($this->mock)
                ->call('loadEncryptedConfigFile')
                    ->withArguments('test.enc', $testFile)
                    ->once()
        ;
        
        $this->assert('test EncryptedConfig::loadConfigFile falls back to parent for non-encrypted files')
            ->given($testFile = $this->testConfigDir . '/test.json')
            ->given($jsonData = '{"key": "value"}')
            ->if(file_put_contents($testFile, $jsonData))
            ->then
            ->variable($this->invoke($this->mock)->loadConfigFile('test.json', $testFile))
                ->isNull()
            ->array($config = $this->mock->getConfig())
                ->hasKey('test.json')
            ->object($config['test.json'])
                ->isEqualTo((object) ['key' => 'value'])
        ;
    }
}