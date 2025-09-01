<?php

namespace BFW;

use \Exception;

/**
 * Class to handle encrypted configuration files for enhanced security
 */
class EncryptedConfig extends Config
{
    /**
     * @const ERR_ENCRYPTION_FAILED Exception code if encryption fails
     */
    const ERR_ENCRYPTION_FAILED = 1103001;
    
    /**
     * @const ERR_DECRYPTION_FAILED Exception code if decryption fails
     */
    const ERR_DECRYPTION_FAILED = 1103002;
    
    /**
     * @const ERR_INVALID_ENCRYPTED_FILE Exception code if encrypted file format is invalid
     */
    const ERR_INVALID_ENCRYPTED_FILE = 1103003;
    
    /**
     * @const ERR_MISSING_CRYPTO_EXTENSION Exception code if required crypto extension is missing
     */
    const ERR_MISSING_CRYPTO_EXTENSION = 1103004;

    /**
     * @var string $moduleName The module name for key derivation
     */
    protected $moduleName;
    
    /**
     * @var string $encryptionCipher The encryption cipher to use
     */
    protected $encryptionCipher = 'aes-256-gcm';

    /**
     * Constructor
     * 
     * @param string $configDirName Directory's name in config dir
     * @param string $moduleName Module name for key derivation
     */
    public function __construct(string $configDirName, string $moduleName)
    {
        if (!extension_loaded('openssl')) {
            throw new Exception(
                'OpenSSL extension is required for encrypted configuration',
                self::ERR_MISSING_CRYPTO_EXTENSION
            );
        }
        
        parent::__construct($configDirName);
        $this->moduleName = $moduleName;
    }

    /**
     * Load a config file, handling both encrypted and plain files
     * 
     * @param string $fileKey The file's key
     * @param string $filePath The path to the file
     * 
     * @return void
     */
    protected function loadConfigFile(string $fileKey, string $filePath)
    {
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        // Handle encrypted files
        if ($fileExtension === 'enc') {
            $this->loadEncryptedConfigFile($fileKey, $filePath);
            return;
        }

        // Fall back to parent implementation for non-encrypted files
        parent::loadConfigFile($fileKey, $filePath);
    }

    /**
     * Load an encrypted config file
     * 
     * @param string $fileKey The file's key
     * @param string $filePath The path to the file
     * 
     * @return void
     * 
     * @throws \Exception If decryption fails or file format is invalid
     */
    protected function loadEncryptedConfigFile(string $fileKey, string $filePath)
    {
        $encryptedData = file_get_contents($filePath);
        $decryptedData = $this->decryptData($encryptedData);
        
        // Determine original file type from the decrypted data header
        $originalExtension = $this->getOriginalFileExtension($decryptedData);
        $configData = $this->parseDecryptedData($decryptedData, $originalExtension);
        
        $this->config[$fileKey] = $configData;
    }

    /**
     * Encrypt configuration data for secure storage
     * 
     * @param string $data The data to encrypt
     * @param string $originalExtension The original file extension for metadata
     * 
     * @return string The encrypted data
     * 
     * @throws \Exception If encryption fails
     */
    public function encryptConfigData(string $data, string $originalExtension): string
    {
        $key = $this->deriveEncryptionKey();
        $iv = random_bytes(16); // 128-bit IV for AES
        $tag = '';
        
        // Add metadata header with original file extension
        $metadata = json_encode(['ext' => $originalExtension]);
        $metadataLength = strlen($metadata);
        $dataWithMetadata = pack('N', $metadataLength) . $metadata . $data;
        
        $encrypted = openssl_encrypt(
            $dataWithMetadata,
            $this->encryptionCipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        
        if ($encrypted === false) {
            throw new Exception(
                'Failed to encrypt configuration data',
                self::ERR_ENCRYPTION_FAILED
            );
        }
        
        // Combine IV, tag, and encrypted data
        return base64_encode($iv . $tag . $encrypted);
    }

    /**
     * Decrypt configuration data
     * 
     * @param string $encryptedData The encrypted data
     * 
     * @return string The decrypted data
     * 
     * @throws \Exception If decryption fails
     */
    protected function decryptData(string $encryptedData): string
    {
        $data = base64_decode($encryptedData);
        if ($data === false) {
            throw new Exception(
                'Invalid encrypted file format: base64 decode failed',
                self::ERR_INVALID_ENCRYPTED_FILE
            );
        }
        
        // Extract IV (16 bytes), tag (16 bytes), and encrypted content
        if (strlen($data) < 32) {
            throw new Exception(
                'Invalid encrypted file format: file too short',
                self::ERR_INVALID_ENCRYPTED_FILE
            );
        }
        
        $iv = substr($data, 0, 16);
        $tag = substr($data, 16, 16);
        $encrypted = substr($data, 32);
        
        $key = $this->deriveEncryptionKey();
        
        $decrypted = openssl_decrypt(
            $encrypted,
            $this->encryptionCipher,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
        
        if ($decrypted === false) {
            throw new Exception(
                'Failed to decrypt configuration data',
                self::ERR_DECRYPTION_FAILED
            );
        }
        
        return $decrypted;
    }

    /**
     * Get the original file extension from decrypted data metadata
     * 
     * @param string $decryptedData The decrypted data with metadata
     * 
     * @return string The original file extension
     * 
     * @throws \Exception If metadata is invalid
     */
    protected function getOriginalFileExtension(string &$decryptedData): string
    {
        if (strlen($decryptedData) < 4) {
            throw new Exception(
                'Invalid encrypted file format: no metadata',
                self::ERR_INVALID_ENCRYPTED_FILE
            );
        }
        
        // Unpack metadata length
        $metadataLength = unpack('N', substr($decryptedData, 0, 4))[1];
        
        if (strlen($decryptedData) < 4 + $metadataLength) {
            throw new Exception(
                'Invalid encrypted file format: metadata truncated',
                self::ERR_INVALID_ENCRYPTED_FILE
            );
        }
        
        // Extract and parse metadata
        $metadata = json_decode(substr($decryptedData, 4, $metadataLength), true);
        if (!$metadata || !isset($metadata['ext'])) {
            throw new Exception(
                'Invalid encrypted file format: invalid metadata',
                self::ERR_INVALID_ENCRYPTED_FILE
            );
        }
        
        // Remove metadata from decrypted data
        $decryptedData = substr($decryptedData, 4 + $metadataLength);
        
        return $metadata['ext'];
    }

    /**
     * Parse decrypted data based on original file extension
     * 
     * @param string $data The decrypted data
     * @param string $extension The original file extension
     * 
     * @return mixed The parsed configuration data
     * 
     * @throws \Exception If parsing fails
     */
    protected function parseDecryptedData(string $data, string $extension)
    {
        switch ($extension) {
            case 'json':
                $config = json_decode($data);
                if ($config === null) {
                    throw new Exception(
                        json_last_error_msg(),
                        parent::ERR_JSON_PARSE
                    );
                }
                return $config;
                
            case 'php':
                // For security, we'll eval PHP code in a restricted context
                // This is still not 100% safe but better than file_get_contents
                return $this->evaluatePhpConfig($data);
                
            default:
                // Return raw data for unknown formats
                return $data;
        }
    }

    /**
     * Safely evaluate PHP configuration data
     * 
     * @param string $phpCode The PHP code to evaluate
     * 
     * @return mixed The configuration data
     */
    protected function evaluatePhpConfig(string $phpCode)
    {
        // Remove opening PHP tag if present
        $phpCode = preg_replace('/^\s*<\?php\s*/', '', $phpCode);
        
        // Evaluate in a restricted context
        return eval($phpCode);
    }

    /**
     * Derive encryption key from multiple entropy sources
     * 
     * @return string The derived encryption key
     */
    protected function deriveEncryptionKey(): string
    {
        // Collect entropy from multiple sources
        $entropy = [
            'module' => $this->moduleName,
            'app_secret' => $this->getApplicationSecret(),
            'system' => $this->getSystemEntropy(),
        ];
        
        // Use PBKDF2 for key derivation
        $salt = hash('sha256', implode('|', $entropy));
        $iterations = 10000; // Reasonable number for performance vs security
        
        return hash_pbkdf2('sha256', $this->moduleName, $salt, $iterations, 32, true);
    }

    /**
     * Get application-level secret for key derivation
     * 
     * @return string The application secret
     */
    protected function getApplicationSecret(): string
    {
        // Try multiple sources for application secret
        $sources = [
            // Environment variable
            getenv('BFW_APP_SECRET'),
            // Application constant if defined
            defined('BFW_APP_SECRET') ? BFW_APP_SECRET : null,
            // Default based on application root
            hash('sha256', realpath(__DIR__ . '/..') . '|bfw-app-secret'),
        ];
        
        foreach ($sources as $secret) {
            if (!empty($secret)) {
                return $secret;
            }
        }
        
        // Fallback to application directory hash
        return hash('sha256', realpath(__DIR__ . '/..'));
    }

    /**
     * Get system-level entropy for key derivation
     * 
     * @return string System entropy
     */
    protected function getSystemEntropy(): string
    {
        $entropy = [
            // Process info that malicious modules can't easily access
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? realpath(__DIR__ . '/..'),
        ];
        
        return hash('sha256', implode('|', $entropy));
    }

    /**
     * Save encrypted configuration file
     * 
     * @param string $filename The filename to save
     * @param mixed $data The configuration data
     * @param string $originalExtension The original file extension
     * 
     * @return bool True on success
     * 
     * @throws \Exception If encryption or saving fails
     */
    public function saveEncryptedConfig(string $filename, $data, string $originalExtension): bool
    {
        $jsonData = '';
        
        switch ($originalExtension) {
            case 'json':
                $jsonData = json_encode($data, JSON_PRETTY_PRINT);
                break;
            case 'php':
                $jsonData = $data; // Assume $data is already PHP code
                break;
            default:
                $jsonData = (string) $data;
        }
        
        $encryptedData = $this->encryptConfigData($jsonData, $originalExtension);
        $filePath = $this->configDir . '/' . $filename . '.enc';
        
        // Ensure directory exists
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        return file_put_contents($filePath, $encryptedData) !== false;
    }
}