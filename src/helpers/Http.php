<?php

namespace BFW\Helpers;

/**
 * Helpers for http requests/responses
 */
class Http
{
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
        $page,
        $permanent = false,
        $callExit = false
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
}
