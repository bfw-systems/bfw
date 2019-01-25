<?php

namespace BFW\Helpers;

/**
 * Helpers for http requests/responses
 */
class Http
{
    /**
     * Return the class name of the secure helper.
     * Allow to extends the secure helper used by method here
     * 
     * @return string
     */
    protected static function getSecureHelpersName(): string
    {
        return '\BFW\Helpers\Secure';
    }

    /**
     * Create a http redirect and kill the script
     * 
     * @param string $page The page where is the redirect
     * @param boolean $permanent (default false) If the redirect is permanent
     * @param boolean $callExit (default false) If at true, the exit function
     *  will be called.
     * 
     * @return void
     */
    public static function redirect(
        string $page,
        bool $permanent = false,
        bool $callExit = false
    ) {
        $httpStatus = 302;
        if ($permanent === true) {
            $httpStatus = 301;
        }

        http_response_code($httpStatus);
        header('Location: '.$page);
        
        if ($callExit === true) {
            exit;
        }
    }

    /**
     * Get a securised value for a key in $_POST array
     * 
     * @param string $key The key where is the value to securize
     * @param string $type The type of data
     * @param boolean $htmlentities (default: false) If use htmlentities
     *  function to a better security
     * @param boolean $inline (default: true) If array data are inline
     * 
     * @return mixed
     */
    public static function obtainPostKey(
        string $key,
        string $type,
        bool $htmlentities = false,
        bool $inline = true
    ) {
        $currentClass = get_called_class();
        $secure       = $currentClass::getSecureHelpersName();
        
        return $secure::getSecureKeyInArray(
            $_POST,
            $key,
            $type,
            $htmlentities,
            $inline
        );
    }

    /**
     * Get a securised value for a key in $_GET array
     * 
     * @param string $key The key where is the value to securize
     * @param string $type The type of data
     * @param boolean $htmlentities (default: false) If use htmlentities
     *  function to a better security
     * @param boolean $inline (default: true) If array data are inline
     * 
     * @return mixed
     */
    public static function obtainGetKey(
        string $key,
        string $type,
        bool $htmlentities = false,
        bool $inline = true
    ) {
        $currentClass = get_called_class();
        $secure       = $currentClass::getSecureHelpersName();
        
        return $secure::getSecureKeyInArray(
            $_GET,
            $key,
            $type,
            $htmlentities,
            $inline
        );
    }
    
    /**
     * Obtain many securised keys from $_POST array in one time
     * 
     * @see \BFW\Helpers\Secure::getSecurisedManyKeys
     * 
     * @param array $keysList The key list to obtain.
     * @param boolean $throwOnError (defaut true) If a key not exist, throw an
     *  exception. If false, the value will be null into returned array
     * 
     * @return array
     * 
     * @throws \Exception If a key is not found and if $throwOnError is true
     */
    public static function obtainManyPostKeys(
        array $keysList,
        bool $throwOnError = true
    ): array {
        $currentClass = get_called_class();
        $secure       = $currentClass::getSecureHelpersName();
        
        return $secure::getManySecureKeys($_POST, $keysList, $throwOnError);
    }
    
    /**
     * Obtain many securised keys from $_GET array in one time
     * 
     * @see \BFW\Helpers\Secure::getSecurisedManyKeys
     * 
     * @param array $keysList The key list to obtain.
     * @param boolean $throwOnError (defaut true) If a key not exist, throw an
     *  exception. If false, the value will be null into returned array
     * 
     * @return array
     * 
     * @throws \Exception If a key is not found and if $throwOnError is true
     */
    public static function obtainManyGetKeys(
        array $keysList,
        bool $throwOnError = true
    ): array {
        $currentClass = get_called_class();
        $secure       = $currentClass::getSecureHelpersName();
        
        return $secure::getManySecureKeys($_GET, $keysList, $throwOnError);
    }
}
