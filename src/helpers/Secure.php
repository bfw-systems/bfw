<?php

namespace BFW\Helpers;

use \Exception;

/**
 * Helpers to securize data
 */
class Secure
{
    /**
     * @const ERR_SECURE_UNKNOWN_TYPE Exception code if the data into the
     * method secure() is not a predefined type.
     */
    const ERR_SECURE_UNKNOWN_TYPE = 1607001;
    
    /**
     * @const ERR_SECURE_ARRAY_KEY_NOT_EXIST If the asked key not exist into
     * the array to secure.
     */
    const ERR_SECURE_ARRAY_KEY_NOT_EXIST = 1607002;
    
    /**
     * @const ERR_OBTAIN_KEY Exception code if the key asked not exist
     */
    const ERR_OBTAIN_KEY = 1607003;
    
    /**
     * Hash a string
     * 
     * @param string $val String to hash
     * 
     * @return string
     */
    public static function hash($val)
    {
        return hash('sha256', md5($val));
    }

    /**
     * Securize a string for some types with filter_var function.
     * 
     * @param mixed $data String to securize
     * @param string $type Type of filter
     * 
     * @return mixed
     * 
     * @throws Exception If the type is unknown
     */
    public static function securiseKnownTypes($data, $type)
    {
        $filterType = 'text';

        if ($type === 'int' || $type === 'integer') {
            $filterType = FILTER_VALIDATE_INT;
        } elseif ($type === 'float' || $type === 'double') {
            $filterType = FILTER_VALIDATE_FLOAT;
        } elseif ($type === 'bool' || $type === 'boolean') {
            $filterType = FILTER_VALIDATE_BOOLEAN;
        } elseif ($type === 'email') {
            $filterType = FILTER_VALIDATE_EMAIL;
        }

        if ($filterType === 'text') {
            throw new Exception('Unknown type', self::ERR_SECURE_UNKNOWN_TYPE);
        }

        return filter_var($data, $filterType);
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
     * @throws Exception If an error with a type of data
     */
    public static function securise($data, $type, $htmlentities)
    {
        $currentClass = get_called_class();
        
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                unset($data[$key]);

                $key = $currentClass::securise($key, gettype($key), true);
                $val = $currentClass::securise($val, $type, $htmlentities);

                $data[$key] = $val;
            }

            return $data;
        }

        try {
            return $currentClass::securiseKnownTypes($data, $type);
        } catch (Exception $ex) {
            if ($ex->getCode() !== self::ERR_SECURE_UNKNOWN_TYPE) {
                throw new Exception($ex->getCode(), $ex->getMessage());
            }
            //Else : Use securise text type
        }

        $sqlSecureMethod = $currentClass::getSqlSecureMethod();
        if ($sqlSecureMethod !== false) {
            $data = $sqlSecureMethod($data);
        } else {
            $data = addslashes($data);
        }

        if ($htmlentities === true) {
            $data = htmlentities($data, ENT_COMPAT | ENT_HTML401);
        }

        return $data;
    }

    /**
     * Get the sqlSecure function declared in bfw config file
     * 
     * @return boolean|string
     */
    public static function getSqlSecureMethod()
    {
        $app       = \BFW\Application::getInstance();
        $secureFct = $app->getConfig()->getValue(
            'sqlSecureMethod',
            'global.php'
        );

        if (!is_callable($secureFct, false)) {
            return false;
        }

        return $secureFct;
    }

    /**
     * Securise the value of an array key for a declared type.
     * 
     * @param array &$array The array where is the key
     * @param string $key The key where is the value to securize
     * @param string $type The type of data
     * @param boolean $htmlentities (default: false) If use htmlentities
     *  function to a better security
     * 
     * @return mixed
     * 
     * @throws Exception If the key not exist in array
     */
    public static function getSecurisedKeyInArray(
        &$array,
        $key,
        $type,
        $htmlentities = false
    ) {
        if (!isset($array[$key])) {
            throw new Exception(
                'The key '.$key.' not exist',
                self::ERR_SECURE_ARRAY_KEY_NOT_EXIST
            );
        }

        $currentClass = get_called_class();
        return $currentClass::securise(
            trim($array[$key]),
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
     *  case, the properties "type" contain the value type, and the "htmlenties"
     *  property contain the boolean who indicate if secure system 
     *  will use htmlentities.
     * @param boolean $throwOnError (defaut true) If a key not exist, throw an
     *  exception. If false, the value will be null into returned array
     * 
     * @return array
     * 
     * @throws Exception If a key is not found and if $throwOnError is true
     */
    public static function getSecurisedManyKeys(
        &$arraySrc,
        $keysList,
        $throwOnError = true
    ) {
        $currentClass = get_called_class();
        $result       = [];
        
        foreach ($keysList as $keyName => $infos) {
            if (!is_object($infos)) {
                $infos = (object) [
                    'type'         => $infos,
                    'htmlentities' => false
                ];
            }
            
            try {
                $result[$keyName] = $currentClass::getSecurisedKeyInArray(
                    $arraySrc,
                    $keyName,
                    $infos->type,
                    $infos->htmlentities
                );
            } catch (Exception $ex) {
                if ($throwOnError === true) {
                    throw new Exception(
                        'Error to obtain the key '.$keyName,
                        self::ERR_OBTAIN_KEY,
                        $ex
                    );
                } else {
                    $result[$keyName] = null;
                }
            }
        }
        
        return $result;
    }
}
