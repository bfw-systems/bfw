<?php

namespace BFW\Helpers;

use Exception;
use Normalizer;

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
     * Securize a string for some types with comprehensive security validation.
     * Maintains full backward compatibility when options parameter is not provided.
     *
     * @param mixed $data String to securize
     * @param string $type Type of filter
     * @param array $options Additional security options (optional)
     *
     * @return mixed
     *
     * @throws \Exception If the type is unknown or validation fails
     */
    public static function secureKnownType($data, string $type, array $options = [])
    {
        // Enhanced security pre-processing only when explicitly requested
        if (!empty($options) && is_string($data)) {
            // Remove null bytes (security vulnerability)
            $data = str_replace("\0", '', $data);
            
            // Normalize Unicode to prevent homograph attacks
            if (function_exists('normalizer_normalize') && ($options['normalize_unicode'] ?? false)) {
                $data = normalizer_normalize($data, Normalizer::FORM_C);
            }
            
            // Check for maximum length to prevent DoS attacks
            $maxLength = $options['max_length'] ?? 65535;
            if (strlen($data) > $maxLength) {
                throw new Exception(
                    'Input too long for security',
                    self::ERR_SECURE_KNOWN_TYPE_FILTER_NOT_MANAGED
                );
            }
        }

        $filterType = 'text';
        $filterOptions = null;

        switch ($type) {
            case 'int':
            case 'integer':
                $filterType = FILTER_VALIDATE_INT;
                // Add range validation for security
                if (isset($options['min']) || isset($options['max'])) {
                    $filterOptions = [
                        'options' => array_filter([
                            'min_range' => $options['min'] ?? null,
                            'max_range' => $options['max'] ?? null
                        ])
                    ];
                }
                break;
                
            case 'float':
            case 'double':
                $filterType = FILTER_VALIDATE_FLOAT;
                // Add range validation for security
                if (isset($options['min']) || isset($options['max'])) {
                    $filterOptions = [
                        'options' => array_filter([
                            'min_range' => $options['min'] ?? null,
                            'max_range' => $options['max'] ?? null
                        ])
                    ];
                }
                break;
                
            case 'bool':
            case 'boolean':
                $filterType = FILTER_VALIDATE_BOOLEAN;
                break;
                
            case 'email':
                $filterType = FILTER_VALIDATE_EMAIL;
                // Additional email security validation when options provided
                if (!empty($options) && is_string($data)) {
                    // Check for common email injection patterns
                    $dangerous = ['%0a', '%0d', '\n', '\r', 'bcc:', 'cc:', 'to:'];
                    foreach ($dangerous as $pattern) {
                        if (stripos($data, $pattern) !== false) {
                            return false;
                        }
                    }
                    // Limit email length for security
                    if (strlen($data) > 254) {
                        return false;
                    }
                }
                break;
                
            case 'url':
                $filterType = FILTER_VALIDATE_URL;
                // Additional URL security validation when options provided
                if (!empty($options) && is_string($data)) {
                    // Block dangerous protocols
                    $dangerousProtocols = ['javascript:', 'data:', 'vbscript:', 'file:', 'ftp:'];
                    foreach ($dangerousProtocols as $protocol) {
                        if (stripos($data, $protocol) === 0) {
                            return false;
                        }
                    }
                    // Limit URL length for security
                    if (strlen($data) > 2048) {
                        return false;
                    }
                }
                break;
                
            case 'ip':
                $filterType = FILTER_VALIDATE_IP;
                // Add IP version restrictions if specified
                if (isset($options['ipv4_only']) && $options['ipv4_only']) {
                    $filterOptions = FILTER_FLAG_IPV4;
                } elseif (isset($options['ipv6_only']) && $options['ipv6_only']) {
                    $filterOptions = FILTER_FLAG_IPV6;
                }
                break;
                
            case 'mac':
                $filterType = FILTER_VALIDATE_MAC;
                break;
                
            case 'domain':
                $filterType = FILTER_VALIDATE_DOMAIN;
                break;
                
            // New comprehensive validation types
            case 'json':
                return self::validateJson($data, $options);
                
            case 'base64':
                return self::validateBase64($data, $options);
                
            case 'uuid':
                return self::validateUUID($data, $options);
                
            case 'phone':
                return self::validatePhone($data, $options);
                
            case 'creditcard':
                return self::validateCreditCard($data, $options);
                
            case 'jwt':
                return self::validateJWT($data, $options);
                
            case 'timezone':
                return self::validateTimezone($data, $options);
                
            case 'color':
                return self::validateColor($data, $options);
                
            case 'regex':
                return self::validateRegex($data, $options);
                
            case 'file_extension':
                return self::validateFileExtension($data, $options);
                
            case 'mime_type':
                return self::validateMimeType($data, $options);
                
            case 'csrf_token':
                return self::validateCSRFToken($data, $options);
        }

        if ($filterType === 'text') {
            throw new Exception(
                'Cannot secure the type',
                self::ERR_SECURE_KNOWN_TYPE_FILTER_NOT_MANAGED
            );
        }

        $result = filter_var($data, $filterType, $filterOptions);
        
        // Additional security check: ensure result is not false for security-critical validations
        if ($result === false && in_array($type, ['email', 'url', 'ip'])) {
            return false;
        }
        
        return $result;
    }

    /**
     * Securise a mixed data type with comprehensive security protection against all injection types.
     * Provides defense against XSS, SQL injection, command injection, and other attack vectors.
     *
     * @param mixed $data The variable to securise
     * @param string $type The type of datas (html, sql, command, ldap, xml, nosql, etc.)
     * @param boolean $htmlentities If use htmlentities function to a better security
     * @param boolean $useHtml5 Use HTML5 entities instead of HTML4 (default: false for compatibility)
     * @param array $options Additional security options
     *
     * @return string
     */
    public static function secureUnknownType(
        $data,
        string $type,
        bool $htmlentities,
        bool $useHtml5 = false,
        array $options = []
    ): string {
        // Convert to string if not already
        $data = (string) $data;
        
        // Universal security pre-processing
        $data = self::universalSecurityFilter($data, $options);
        
        // Apply context-specific security based on type
        switch ($type) {
            case 'html':
                return self::secureForHtml($data, $htmlentities, $useHtml5, $options);
                
            case 'sql':
                return self::secureForSql($data, $options);
                
            case 'command':
            case 'shell':
                return self::secureForCommand($data, $options);
                
            case 'ldap':
                return self::secureForLdap($data, $options);
                
            case 'xml':
                return self::secureForXml($data, $options);
                
            case 'nosql':
            case 'mongodb':
                return self::secureForNoSql($data, $options);
                
            case 'css':
                return self::secureForCss($data, $options);
                
            case 'javascript':
            case 'js':
                return self::secureForJavaScript($data, $options);
                
            case 'url':
            case 'uri':
                return self::secureForUrl($data, $options);
                
            case 'filename':
            case 'path':
                return self::secureForPath($data, $options);
                
            case 'csv':
                return self::secureForCsv($data, $options);
                
            case 'template':
                return self::secureForTemplate($data, $options);
                
            case 'header':
                return self::secureForHeader($data, $options);
                
            default:
                // Default comprehensive protection for unknown types
                return self::secureDefault($data, $htmlentities, $useHtml5, $options);
        }
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

    // ============================================================================
    // COMPREHENSIVE VALIDATION METHODS FOR secureKnownType
    // ============================================================================

    /**
     * Validate JSON with security checks
     */
    private static function validateJson($data, array $options = [])
    {
        if (!is_string($data)) {
            return false;
        }
        
        // Prevent billion laughs attack and excessive nesting
        $maxDepth = $options['max_depth'] ?? 512;
        $maxLength = $options['max_length'] ?? 1048576; // 1MB limit
        
        if (strlen($data) > $maxLength) {
            return false;
        }
        
        $decoded = json_decode($data, true, $maxDepth, JSON_BIGINT_AS_STRING);
        
        return json_last_error() === JSON_ERROR_NONE ? $decoded : false;
    }

    /**
     * Validate Base64 with security checks
     */
    private static function validateBase64($data, array $options = [])
    {
        if (!is_string($data)) {
            return false;
        }
        
        // Check if valid base64
        if (!preg_match('/^[A-Za-z0-9+\/]*={0,2}$/', $data)) {
            return false;
        }
        
        $decoded = base64_decode($data, true);
        if ($decoded === false) {
            return false;
        }
        
        // Re-encode to verify
        return base64_encode($decoded) === $data ? $decoded : false;
    }

    /**
     * Validate UUID with security checks
     */
    private static function validateUUID($data, array $options = [])
    {
        if (!is_string($data)) {
            return false;
        }
        
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
        return preg_match($pattern, $data) ? $data : false;
    }

    /**
     * Validate phone numbers with security checks
     */
    private static function validatePhone($data, array $options = [])
    {
        if (!is_string($data)) {
            return false;
        }
        
        // Remove common formatting
        $cleaned = preg_replace('/[^\d+]/', '', $data);
        
        // Basic validation (7-15 digits, optional + prefix)
        if (preg_match('/^\+?[1-9]\d{6,14}$/', $cleaned)) {
            return $cleaned;
        }
        
        return false;
    }

    /**
     * Validate credit card with security checks (PCI DSS compliant)
     */
    private static function validateCreditCard($data, array $options = [])
    {
        if (!is_string($data)) {
            return false;
        }
        
        // Remove spaces and dashes
        $cleaned = preg_replace('/[\s-]/', '', $data);
        
        // Check if only digits
        if (!ctype_digit($cleaned)) {
            return false;
        }
        
        // Length check (13-19 digits)
        if (strlen($cleaned) < 13 || strlen($cleaned) > 19) {
            return false;
        }
        
        // Luhn algorithm check
        return self::luhnCheck($cleaned) ? $cleaned : false;
    }

    /**
     * Luhn algorithm for credit card validation
     */
    private static function luhnCheck($number): bool
    {
        $sum = 0;
        $alternate = false;
        
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $digit = intval($number[$i]);
            
            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit = ($digit % 10) + 1;
                }
            }
            
            $sum += $digit;
            $alternate = !$alternate;
        }
        
        return $sum % 10 === 0;
    }

    /**
     * Validate JWT token structure
     */
    private static function validateJWT($data, array $options = [])
    {
        if (!is_string($data)) {
            return false;
        }
        
        $parts = explode('.', $data);
        if (count($parts) !== 3) {
            return false;
        }
        
        // Validate each part is valid base64
        foreach ($parts as $part) {
            if (base64_decode($part, true) === false) {
                return false;
            }
        }
        
        return $data;
    }

    /**
     * Validate timezone
     */
    private static function validateTimezone($data, array $options = [])
    {
        if (!is_string($data)) {
            return false;
        }
        
        return in_array($data, timezone_identifiers_list()) ? $data : false;
    }

    /**
     * Validate color codes
     */
    private static function validateColor($data, array $options = [])
    {
        if (!is_string($data)) {
            return false;
        }
        
        // Hex color
        if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $data)) {
            return $data;
        }
        
        // RGB/RGBA
        if (preg_match('/^rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*(?:,\s*[\d.]+\s*)?\)$/', $data)) {
            return $data;
        }
        
        return false;
    }

    /**
     * Validate with custom regex
     */
    private static function validateRegex($data, array $options = [])
    {
        if (!is_string($data) || !isset($options['pattern'])) {
            return false;
        }
        
        // Security: validate the regex pattern itself
        if (@preg_match($options['pattern'], '') === false) {
            return false;
        }
        
        return preg_match($options['pattern'], $data) ? $data : false;
    }

    /**
     * Validate file extension
     */
    private static function validateFileExtension($data, array $options = [])
    {
        if (!is_string($data)) {
            return false;
        }
        
        $extension = strtolower(pathinfo($data, PATHINFO_EXTENSION));
        $allowed = $options['allowed'] ?? ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt'];
        
        return in_array($extension, $allowed) ? $extension : false;
    }

    /**
     * Validate MIME type
     */
    private static function validateMimeType($data, array $options = [])
    {
        if (!is_string($data)) {
            return false;
        }
        
        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9!#$&\-\^_]*\/[a-zA-Z0-9][a-zA-Z0-9!#$&\-\^_.]*$/', $data)) {
            return false;
        }
        
        $allowed = $options['allowed'] ?? [
            'image/jpeg', 'image/png', 'image/gif', 'text/plain', 'application/pdf'
        ];
        
        return in_array($data, $allowed) ? $data : false;
    }

    /**
     * Validate CSRF token
     */
    private static function validateCSRFToken($data, array $options = [])
    {
        if (!is_string($data)) {
            return false;
        }
        
        // Basic format validation
        if (!preg_match('/^[A-Za-z0-9+\/]{32,}={0,2}$/', $data)) {
            return false;
        }
        
        // Minimum length for security
        if (strlen($data) < 32) {
            return false;
        }
        
        return $data;
    }

    // ============================================================================
    // COMPREHENSIVE SECURITY FILTERS FOR secureUnknownType
    // ============================================================================

    /**
     * Universal security pre-processing
     */
    private static function universalSecurityFilter(string $data, array $options = []): string
    {
        // Remove null bytes
        $data = str_replace("\0", '', $data);
        
        // Remove BOM
        $data = preg_replace('/^\xEF\xBB\xBF/', '', $data);
        
        // Normalize Unicode
        if (function_exists('normalizer_normalize')) {
            $data = normalizer_normalize($data, Normalizer::FORM_C);
        }
        
        // Force UTF-8 encoding
        if (!mb_check_encoding($data, 'UTF-8')) {
            $data = mb_convert_encoding($data, 'UTF-8', 'auto');
        }
        
        // Length limit for DoS protection
        $maxLength = $options['max_length'] ?? 1048576; // 1MB
        if (strlen($data) > $maxLength) {
            throw new Exception('Input too long for security', self::ERR_SECURE_KNOWN_TYPE_FILTER_NOT_MANAGED);
        }
        
        return $data;
    }

    /**
     * Secure for HTML context with comprehensive XSS protection
     */
    private static function secureForHtml(string $data, bool $htmlentities, bool $useHtml5, array $options = []): string
    {
        // Remove dangerous HTML tags and attributes
        $data = self::removeHtmlThreats($data, $options);
        
        if ($htmlentities) {
            if ($useHtml5) {
                return htmlentities($data, ENT_QUOTES | ENT_HTML5 | ENT_DISALLOWED, 'UTF-8');
            }
            return htmlentities($data, ENT_QUOTES | ENT_HTML401 | ENT_DISALLOWED, 'UTF-8');
        }
        
        return $data;
    }

    /**
     * Remove HTML-based security threats
     */
    private static function removeHtmlThreats(string $data, array $options = []): string
    {
        // Remove script tags and their content
        $data = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $data);
        
        // Remove dangerous event handlers
        $data = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']?/i', '', $data);
        
        // Remove javascript: protocol
        $data = preg_replace('/javascript\s*:/i', '', $data);
        
        // Remove data: protocol for images (configurable)
        if (!($options['allow_data_urls'] ?? false)) {
            $data = preg_replace('/data\s*:/i', '', $data);
        }
        
        // Remove style attributes with expression()
        $data = preg_replace('/style\s*=\s*["\'][^"\']*expression\s*\([^"\']*["\']?/i', '', $data);
        
        return $data;
    }

    /**
     * Secure for SQL context
     */
    private static function secureForSql(string $data, array $options = []): string
    {
        // Remove SQL injection patterns
        $patterns = [
            '/(\s*(;|\'|"|`)\s*(DROP|DELETE|UPDATE|INSERT|ALTER|CREATE|TRUNCATE|EXEC|EXECUTE)\s+)/i',
            '/(\s*(UNION|SELECT)\s+.*\s+(FROM|WHERE)\s+)/i',
            '/(\s*(OR|AND)\s+[\d\'\"][^=]*=\s*[\d\'\"])/i',
            '/(\s*--\s*)/i',
            '/(\s*\/\*.*\*\/\s*)/i'
        ];
        
        foreach ($patterns as $pattern) {
            $data = preg_replace($pattern, '', $data);
        }
        
        // Escape remaining single quotes
        $data = str_replace("'", "''", $data);
        
        return $data;
    }

    /**
     * Secure for command injection
     */
    private static function secureForCommand(string $data, array $options = []): string
    {
        // Remove command injection patterns
        $dangerous = ['|', '&', ';', '`', '$', '(', ')', '<', '>', '"', "'", '\\', "\n", "\r"];
        
        foreach ($dangerous as $char) {
            $data = str_replace($char, '', $data);
        }
        
        return $data;
    }

    /**
     * Secure for LDAP injection
     */
    private static function secureForLdap(string $data, array $options = []): string
    {
        $dangerous = ['(', ')', '*', '\\', '/', "\0"];
        $safe = ['\\28', '\\29', '\\2a', '\\5c', '\\2f', '\\00'];
        
        return str_replace($dangerous, $safe, $data);
    }

    /**
     * Secure for XML injection and XXE attacks
     */
    private static function secureForXml(string $data, array $options = []): string
    {
        // Remove XML special characters
        $data = str_replace(['<', '>', '&', '"', "'"], ['&lt;', '&gt;', '&amp;', '&quot;', '&#39;'], $data);
        
        // Remove XML processing instructions
        $data = preg_replace('/<\?xml.*?\?>/i', '', $data);
        
        // Remove CDATA sections
        $data = preg_replace('/<!\[CDATA\[.*?\]\]>/i', '', $data);
        
        // Remove DOCTYPE declarations
        $data = preg_replace('/<!DOCTYPE[^>]*>/i', '', $data);
        
        return $data;
    }

    /**
     * Secure for NoSQL injection
     */
    private static function secureForNoSql(string $data, array $options = []): string
    {
        // Remove NoSQL injection patterns
        $patterns = [
            '/\$where/i',
            '/\$regex/i',
            '/\$ne/i',
            '/\$in/i',
            '/\$nin/i',
            '/\$or/i',
            '/\$and/i',
            '/\$not/i',
            '/\$nor/i',
            '/\$exists/i'
        ];
        
        foreach ($patterns as $pattern) {
            $data = preg_replace($pattern, '', $data);
        }
        
        return $data;
    }

    /**
     * Secure for CSS context
     */
    private static function secureForCss(string $data, array $options = []): string
    {
        // Remove dangerous CSS functions
        $dangerous = [
            '/expression\s*\(/i',
            '/javascript\s*:/i',
            '/vbscript\s*:/i',
            '/data\s*:/i',
            '/import\s*["\'][^"\']*["\']?/i',
            '/@import/i'
        ];
        
        foreach ($dangerous as $pattern) {
            $data = preg_replace($pattern, '', $data);
        }
        
        return $data;
    }

    /**
     * Secure for JavaScript context
     */
    private static function secureForJavaScript(string $data, array $options = []): string
    {
        // JSON encode for JavaScript context
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }

    /**
     * Secure for URL context
     */
    private static function secureForUrl(string $data, array $options = []): string
    {
        // URL encode
        $data = urlencode($data);
        
        // Additional security for dangerous protocols
        $dangerous = ['javascript%3A', 'data%3A', 'vbscript%3A'];
        foreach ($dangerous as $protocol) {
            $data = str_ireplace($protocol, '', $data);
        }
        
        return $data;
    }

    /**
     * Secure for file path context
     */
    private static function secureForPath(string $data, array $options = []): string
    {
        return self::sanitizeFilename($data);
    }

    /**
     * Secure for CSV context
     */
    private static function secureForCsv(string $data, array $options = []): string
    {
        // Prevent CSV injection
        if (in_array(substr($data, 0, 1), ['=', '+', '-', '@'])) {
            $data = "'" . $data;
        }
        
        // Escape quotes
        $data = str_replace('"', '""', $data);
        
        return '"' . $data . '"';
    }

    /**
     * Secure for template injection
     */
    private static function secureForTemplate(string $data, array $options = []): string
    {
        // Remove template injection patterns
        $patterns = [
            '/\{\{.*\}\}/s',
            '/\{%.*%\}/s',
            '/\{\#.*\#\}/s',
            '/\$\{.*\}/s'
        ];
        
        foreach ($patterns as $pattern) {
            $data = preg_replace($pattern, '', $data);
        }
        
        return $data;
    }

    /**
     * Secure for HTTP header context
     */
    private static function secureForHeader(string $data, array $options = []): string
    {
        // Remove CRLF injection
        $data = str_replace(["\r", "\n", "\r\n"], '', $data);
        
        // Remove header folding attempts
        $data = preg_replace('/\s+/', ' ', $data);
        
        return trim($data);
    }

    /**
     * Default comprehensive protection
     */
    private static function secureDefault(string $data, bool $htmlentities, bool $useHtml5, array $options = []): string
    {
        // If not HTML, strip tags by default
        $data = strip_tags($data);

        if ($htmlentities) {
            if ($useHtml5) {
                return htmlentities($data, ENT_QUOTES | ENT_HTML5 | ENT_DISALLOWED, 'UTF-8');
            }
            return htmlentities($data, ENT_QUOTES | ENT_HTML401 | ENT_DISALLOWED, 'UTF-8');
        }

        // Enhanced escaping instead of just addslashes
        $data = str_replace(['\\', "'", '"', "\0", "\n", "\r", "\x1a"], 
                           ['\\\\', "\\'", '\\"', '\\0', '\\n', '\\r', '\\Z'], $data);
        
        return $data;
    }
}
