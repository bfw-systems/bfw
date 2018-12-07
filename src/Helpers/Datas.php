<?php

namespace BFW\Helpers;

use \Exception;

/**
 * Helpers to manage datas
 */
class Datas
{
    /**
     * @const ERR_CHECKTYPE_INFOS_FORMAT Exception code if the format of the
     * infos passed to checkType method is not correct.
     */
    const ERR_CHECKTYPE_INFOS_FORMAT = 1604001;
    
    /**
     * @const ERR_CHECKTYPE_DATA_OR_TYPE_VALUE_FORMAT Exception code if data or
     * type used to check the variable has not a correct value.
     */
    const ERR_CHECKTYPE_DATA_OR_TYPE_VALUE_FORMAT = 1604002;
    
    /**
     * Check types of variables
     * 
     * @param array $vars : Variables to check
     *  array(array('type' => 'myType', 'data' => 'myData), array(...)...)
     * 
     * @return boolean
     */
    public static function checkType(array $vars): bool
    {
        foreach ($vars as $var) {
            if (!is_array($var)) {
                throw new Exception(
                    'The informations need for the check is not in a correct format.',
                    self::ERR_CHECKTYPE_INFOS_FORMAT
                );
            }

            if (
                !isset($var['data'])
                || empty($var['type'])
                || (isset($var['type']) && !is_string($var['type']))
            ) {
                throw new Exception(
                    'Items data or type is empty or in an bad format.',
                    self::ERR_CHECKTYPE_DATA_OR_TYPE_VALUE_FORMAT
                );
            }

            if ($var['type'] === 'int') {
                $var['type'] = 'integer';
            } elseif ($var['type'] === 'float') {
                $var['type'] = 'double';
            }

            if (gettype($var['data']) !== $var['type']) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if an email address is valid
     * 
     * @param string $mail The email address to check
     * 
     * @return boolean
     */
    public static function checkMail(string $mail): bool
    {
        $securisedMail = Secure::secureData($mail, 'email', false);
        
        if ($securisedMail === false) {
            return false;
        }
        
        return true;
    }
}
