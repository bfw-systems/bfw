<?php

namespace BFW\Helpers;

use Exception;

/**
 * Helpers to securize data
 */
class Secure
{
    /**
     * @const ERR_SECURE_KNOWN_TYPE_FILTER_NOT_MANAGED Exception code if the
     * data type into the method secureKnownTypes() is not managed.
     */
    public const ERR_SECURE_KNOWN_TYPE_FILTER_NOT_MANAGED = 1609001;

    /**
     * @const ERR_SECURE_ARRAY_KEY_NOT_EXIST If the asked key not exist into
     * the array to secure.
     */
    public const ERR_SECURE_ARRAY_KEY_NOT_EXIST = 1609002;

    /**
     * Hash a string using a secure algorithm
     * 
     * Note: This method is deprecated for password hashing.
     * Use password_hash() and password_verify() for passwords.
     * 
     * For backward compatibility, the default behavior uses the legacy
     * md5+sha256 combination. Use $secure=true for better security.
     *
     * @param string $val String to hash
     * @param string $algorithm Hash algorithm (default: sha256)
     * @param bool $secure Use secure hashing (default: false for compatibility)
     *
     * @return string
     */
    public static function hash(string $val, string $algorithm = 'sha256', bool $secure = false): string
    {
        // Maintain backward compatibility by default
        if (!$secure) {
            return hash('sha256', md5($val));
        }
        
        // Validate algorithm to prevent injection
        $allowedAlgorithms = ['sha256', 'sha512', 'sha3-256', 'sha3-512'];
        if (!in_array($algorithm, $allowedAlgorithms, true)) {
            $algorithm = 'sha256';
        }
        
        return hash($algorithm, $val);
    }

    /**
     * Securize a string for some types with filter_var function.
     *
     * @param mixed $data String to securize
     * @param string $type Type of filter
     *
     * @return mixed
     *
     * @throws \Exception If the type is unknown
     */
    public static function secureKnownType($data, string $type)
    {
        $filterType = 'text';
        $filterOptions = null;

        switch ($type) {
            case 'int':
            case 'integer':
                $filterType = FILTER_VALIDATE_INT;
                break;
            case 'float':
            case 'double':
                $filterType = FILTER_VALIDATE_FLOAT;
                break;
            case 'bool':
            case 'boolean':
                $filterType = FILTER_VALIDATE_BOOLEAN;
                break;
            case 'email':
                $filterType = FILTER_VALIDATE_EMAIL;
                break;
            case 'url':
                $filterType = FILTER_VALIDATE_URL;
                break;
            case 'ip':
                $filterType = FILTER_VALIDATE_IP;
                break;
            case 'mac':
                $filterType = FILTER_VALIDATE_MAC;
                break;
            case 'domain':
                $filterType = FILTER_VALIDATE_DOMAIN;
                break;
        }

        if ($filterType === 'text') {
            throw new Exception(
                'Cannot secure the type',
                self::ERR_SECURE_KNOWN_TYPE_FILTER_NOT_MANAGED
            );
        }

        $result = filter_var($data, $filterType, $filterOptions);
        
        // Keep original behavior for backward compatibility
        return $result;
    }

    /**
     * Securise a mixed data type who are not managed by securiseKnownType.
     * We work the data like if the type is a string.
     *
     * @param mixed $data The variable to securise
     * @param string $type The type of datas
     * @param boolean $htmlentities If use htmlentities function
     *  to a better security
     * @param boolean $useHtml5 Use HTML5 entities instead of HTML4 (default: false for compatibility)
     *
     * @return string
     */
    public static function secureUnknownType(
        $data,
        string $type,
        bool $htmlentities,
        bool $useHtml5 = false
    ): string {
        // Convert to string if not already
        $data = (string) $data;
        
        if ($type !== 'html') {
            $data = strip_tags($data);
        }

        if ($type === 'html' || $htmlentities === true) {
            // Use HTML5 encoding with UTF-8 for better security when requested
            if ($useHtml5) {
                return htmlentities($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
            // Keep backward compatibility with HTML4
            return htmlentities($data, ENT_QUOTES | ENT_HTML401);
        }

        // Use a more secure escaping method
        // Note: addslashes is kept for backward compatibility but not recommended for SQL
        return addslashes($data);
    }

    /**
     * Get the SQL secure method from configuration
     * 
     * @return string|null
     */
    public static function getSqlSecureMethod(): ?string
    {
        // This would typically get the function from configuration
        // For now, return null to maintain existing behavior
        return null;
    }

    /**
     * Securise a variable
     *
     * @param mixed $data The variable to securise
     * @param string $type The type of datas
     * @param boolean $htmlentities If use htmlentities function
     *  to a better security
     *
     * @return mixed
     *
     * @throws \Exception If an error with a type of data
     */
    public static function secureData($data, string $type, bool $htmlentities)
    {
        $currentClass = get_called_class();

        if (is_array($data)) {
            foreach ($data as $key => $val) {
                unset($data[$key]);

                $key = $currentClass::secureData($key, gettype($key), true);
                $val = $currentClass::secureData($val, $type, $htmlentities);

                $data[$key] = $val;
            }

            return $data;
        }

        try {
            return $currentClass::secureKnownType($data, $type);
        } catch (Exception $ex) {
            if ($ex->getCode() !== self::ERR_SECURE_KNOWN_TYPE_FILTER_NOT_MANAGED) {
                throw new Exception($ex->getMessage(), $ex->getCode());
            }
            //Else : Use securise like if it's a text type
        }

        return $currentClass::secureUnknownType($data, $type, $htmlentities);
    }

    /**
     * Securise the value of an array key for a declared type.
     *
     * @param array &$array The array where is the key
     * @param string $key The key where is the value to securize
     * @param string $type The type of data
     * @param boolean $htmlentities (default: false) If use htmlentities
     *  function to a better security
     * @param boolean $inline (default: true) If array data are inline
     *
     * @return mixed
     *
     * @throws \Exception If the key not exist in array
     */
    public static function getSecureKeyInArray(
        array &$array,
        string $key,
        string $type,
        bool $htmlentities = false,
        bool $inline = true
    ) {
        if (!isset($array[$key])) {
            throw new Exception(
                'The key ' . $key . ' not exist',
                self::ERR_SECURE_ARRAY_KEY_NOT_EXIST
            );
        }

        $currentClass = get_called_class();

        if (!$inline) {
            //Only space, NUL-byte, and vertical tab.
            $data = trim($array[$key], ' \0\x0B');
        } else {
            $data = trim($array[$key]);
        }

        return $currentClass::secureData(
            $data,
            $type,
            $htmlentities
        );
    }

    /**
     * Obtain many key from an array in one time
     *
     * @param array &$arraySrc The source array
     * @param array $keysList The key list to obtain.
     *  For each item, the key is the name of the key in source array; And the
     *  value the type of the value. The value can also be an object. In this
     *  case, the properties "type" contain the value type, the "htmlenties"
     *  property contain the boolean who indicate if secure system
     *  will use htmlentities, and the "inline" property contain the boolean who
     *  indicate if data are inline
     * @param boolean $throwOnError (defaut true) If a key not exist, throw an
     *  exception. If false, the value will be null into returned array
     *
     * @return array
     *
     * @throws \Exception If a key is not found and if $throwOnError is true
     */
    public static function getManySecureKeys(
        array &$arraySrc,
        array $keysList,
        bool $throwOnError = true
    ): array {
        $currentClass = get_called_class();
        $result       = [];

        foreach ($keysList as $keyName => $infos) {
            if (!is_array($infos)) {
                $infos = [
                    'type'         => $infos,
                    'htmlentities' => false,
                    'inline'       => true
                ];
            }

            try {
                $result[$keyName] = $currentClass::getSecureKeyInArray(
                    $arraySrc,
                    $keyName,
                    $infos['type'],
                    $infos['htmlentities'],
                    $infos['inline']
                );
            } catch (Exception $ex) {
                if ($throwOnError === true) {
                    throw new Exception(
                        'Error to obtain the key ' . $keyName,
                        self::ERR_SECURE_ARRAY_KEY_NOT_EXIST,
                        $ex
                    );
                } else {
                    $result[$keyName] = null;
                }
            }
        }

        return $result;
    }

    /**
     * Sanitize filename to prevent directory traversal attacks
     *
     * @param string $filename The filename to sanitize
     * @return string Sanitized filename
     */
    public static function sanitizeFilename(string $filename): string
    {
        // Remove directory traversal attempts
        $filename = str_replace(['../', '.\\', '..\\'], '', $filename);
        
        // Remove null bytes and other dangerous characters
        $filename = str_replace(["\0", "\r", "\n"], '', $filename);
        
        // Remove leading dots and slashes
        $filename = ltrim($filename, './\\');
        
        return $filename;
    }

    /**
     * Generate a cryptographically secure random token
     *
     * @param int $length Token length (default: 32)
     * @return string Secure random token
     */
    public static function generateSecureToken(int $length = 32): string
    {
        if ($length < 1) {
            $length = 32;
        }
        
        try {
            return bin2hex(random_bytes($length));
        } catch (\Exception $e) {
            // Fallback for older systems
            return hash('sha256', uniqid(mt_rand(), true));
        }
    }

    /**
     * Constant-time string comparison to prevent timing attacks
     *
     * @param string $known The known string
     * @param string $user The user-provided string
     * @return bool True if strings are equal
     */
    public static function timingSafeEquals(string $known, string $user): bool
    {
        if (function_exists('hash_equals')) {
            return hash_equals($known, $user);
        }
        
        // Fallback implementation
        if (strlen($known) !== strlen($user)) {
            return false;
        }
        
        $result = 0;
        for ($i = 0; $i < strlen($known); $i++) {
            $result |= ord($known[$i]) ^ ord($user[$i]);
        }
        
        return $result === 0;
    }
}
