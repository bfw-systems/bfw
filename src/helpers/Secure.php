<?php

namespace BFW\Helpers;

use \Exception;

/**
 * Helpers to securize data
 */
class Secure
{
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
     * Securize a string for some types with filter_var.
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
            throw new Exception('Unknown type');
        }

        return filter_var($data, $filterType);
    }

    /**
     * Securize a variable
     * 
     * @param mixed $data The variable to securize
     * @param string $type The type of datas
     * @param boolean $htmlentities If use htmlentities function
     *  to more securize
     * 
     * @return mixed
     * 
     * @throws Exception If a error with a type of data
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
            if ($ex->getMessage() !== 'Unknown type') {
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
            $data = htmlentities($data, ENT_COMPAT | ENT_HTML401, 'UTF-8');
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
        $currentClass   = get_called_class();
        $application    = $currentClass::getApplicationInstance();
        $secureFunction = $application->getConfig('sqlSecureMethod');

        if (!is_callable($secureFunction, false)) {
            return false;
        }

        return $secureFunction;
    }
    
    protected static function getApplicationInstance()
    {
        return \BFW\Application::getInstance();
    }

    /**
     * Securize an array key's value for a declared type.
     * 
     * @param array $array The array where is the key
     * @param string $key The key where is the value to securize
     * @param string $type The type of data
     * @param boolean $htmlentities If use htmlentities function
     *  to more securize
     * 
     * @return mixed
     * 
     * @throws Exception If the key not exist in array
     */
    public static function getSecurisedKeyInArray(&$array, $key, $type, $htmlentities = false)
    {
        if (!isset($array[$key])) {
            throw new Exception('The key '.$key.' not exist');
        }

        $currentClass = get_called_class();
        return $currentClass::securise(trim($array[$key]), $type, $htmlentities);
    }

    /**
     * Get a securized value for a key in $_POST array
     * 
     * @param string $key The key where is the value to securize
     * @param string $type The type of data
     * @param boolean $htmlentities If use htmlentities function
     *  to more securize
     * 
     * @return mixed
     */
    public static function getSecurisedPostKey($key, $type, $htmlentities = false)
    {
        $currentClass = get_called_class();
        return $currentClass::getSecurisedKeyInArray($_POST, $key, $type, $htmlentities);
    }

    /**
     * Get a securized value for a key in $_GET array
     * 
     * @param string $key The key where is the value to securize
     * @param string $type The type of data
     * @param boolean $htmlentities If use htmlentities function
     *  to more securize
     * 
     * @return mixed
     */
    public static function getSecurisedGetKey($key, $type, $htmlentities = false)
    {
        $currentClass = get_called_class();
        return $currentClass::getSecurisedKeyInArray($_GET, $key, $type, $htmlentities);
    }
}
