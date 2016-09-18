<?php

namespace BFW\Helpers;

/**
 * Helpers to manage strings
 */
class String
{
    /**
     * Override php function nl2br
     * Native function add a "<br>" after "\n". She's not replace "\n".
     * 
     * @param string $str The input string
     * 
     * @return string
     */
    public static function nl2br($str)
    {
        return str_replace("\n", '<br>', $str);
    }
}
