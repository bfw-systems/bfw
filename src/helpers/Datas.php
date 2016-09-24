<?php

namespace BFW\Helpers;

/**
 * Helpers to manage datas
 */
class Datas
{
    /**
     * Check types of variables
     * 
     * @param array $vars : Variables to check
     *  array(array('type' => 'monType', 'data' => 'mesData), array(...)...)
     * 
     * @return boolean
     */
    public static function checkType($vars)
    {
        if (!is_array($vars)) {
            return false;
        }

        foreach ($vars as $var) {
            if (!is_array($var)) {
                return false;
            }

            if (empty($var['type']) || !isset($var['data'])) {
                return false;
            }

            if (!is_string($var['type'])) {
                return false;
            }

            if ($var['type'] === 'int') {
                $var['type'] = 'integer';
            }

            if ($var['type'] === 'float') {
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
    public static function checkMail($mail)
    {
        $securisedMail = Secure::securise($mail, 'email', false);
        
        if ($securisedMail === false) {
            return false;
        }
        
        return true;
    }
}
