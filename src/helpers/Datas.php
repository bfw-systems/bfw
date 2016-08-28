<?php

namespace BFW\Helpers;

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
    public static function checkTypes($vars)
    {
        if (!is_array($vars)) {
            return false;
        }

        foreach ($vars as $var) {
            if (!is_array($var)) {
                return false;
            }

            if (!(!empty($var['type']) && isset($var['data']))) {
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
}