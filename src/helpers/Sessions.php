<?php

namespace BFW\Helpers;

/**
 * Helpers to manage sessions
 */
class Sessions
{
    /**
     * Check if session is started
     * 
     * @link http://fr2.php.net/manual/en/function.session-status.php#113468
     * 
     * @return boolean
     */
    public static function isStarted()
    {
        if (PHP_SAPI === 'cli') {
            return false;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            return true;
        }

        return false;
    }
}
