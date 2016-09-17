<?php

namespace BFW\Helpers;

use \Exception;

/**
 * Helpers for cli applications
 */
class Cli
{
    /**
     * Get parameters passed to script in cli command
     * Add BFW obligatory parameters
     * 
     * @see http://php.net/manual/en/function.getopt.php
     * 
     * @param string $options Each character in this string will be used as
     *      option characters and matched against options passed to the script
     * @param array $longopts An array of options. Each element in this array
     *      will be used as option strings and matched against options passed
     *      to the script
     * 
     * @return array
     */
    public static function getCliParams($options, $longopts = array())
    {
        $longopts = array_merge($longopts, array('type_site::'));
        $opt      = getopt('f:'.$options, $longopts);

        unset($opt['f']);

        return $opt;
    }

    /**
     * Display a message in the console
     * 
     * @param string $msg Message to display
     * @param string|null $colorTxt (default null) Text color
     * @param string|null $colorBg (default null) Background color
     * @param string $style (default "normal") Style for the message (bold etc)
     * 
     * @return void
     */
    public static function displayMsg($msg, $colorTxt = null, $colorBg = null, $style = 'normal')
    {
        if ($colorTxt == null) {
            echo $msg."\n";
            return;
        }

        //Gestion cas avec couleur
        $styleNum = self::styleForShell($style);
        if ($styleNum === false) {
            $styleNum = self::styleForShell('normal');
        }

        $colorTxtNum = self::colorForShell('white', 'txt');
        if ($colorTxt !== null) {
            $colorTxtNumArg = self::colorForShell($colorTxt, 'txt');

            if ($colorTxtNumArg !== false) {
                $colorTxtNum = $colorTxtNumArg;
            }
        }

        $colorBgNum = self::colorForShell('black', 'bg');
        if ($colorBg !== null) {
            $colorBgNumArg = self::colorForShell($colorBg, 'bg');

            if ($colorBgNumArg !== false) {
                $colorBgNum = $colorBgNumArg;
            }
        }

        echo "\033[".$styleNum.";".$colorBgNum.";"
            .$colorTxtNum
            ."m".$msg."\033[0m\n";
    }

    /**
     * Convert text color to shell value
     * 
     * @param string $color The humain color text
     * @param string $type ("txt"|"bg") If the color is for text of background
     * 
     * @return integer|boolean
     * 
     * @throws Exception If the color is not available
     */
    public static function colorForShell($color, $type)
    {
        $colorList = [
            'black'   => 0,
            'red'     => 1,
            'green'   => 2,
            'yellow'  => 3,
            'blue'    => 4,
            'magenta' => 5,
            'cyan'    => 6,
            'white'   => 7
        ];

        if (!in_array($color, $colorList)) {
            throw new Exception('Color '.$color.' is not available in function.');
        }

        if ($type === 'txt') {
            return $colorList[$color] + 30;
        } elseif ($type === 'bg') {
            return $colorList[$color] + 40;
        }

        return false;
    }

    /**
     * Convert a humain style text to shell value
     * 
     * @param string $style The style value
     * 
     * @return boolean|int
     */
    public static function styleForShell($style)
    {
        $styleList = [
            'normal'        => 0,
            'bold'          => 1,
            'not-bold'      => 21,
            'underline'     => 4,
            'not-underline' => 24,
            'blink'         => 5,
            'not-blink'     => 25,
            'reverse'       => 7,
            'not-reverse'   => 27
        ];

        if (!in_array($style, $styleList)) {
            return false;
        }

        return $styleList[$style];
    }
}
