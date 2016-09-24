<?php

namespace BFW\Helpers;

use \Exception;

/**
 * Helpers for cli applications
 */
class Cli
{
    /**
     * Display a message in the console without a line break
     * 
     * @param string $msg Message to display
     * @param string $colorTxt (default "white") Text color
     * @param string $colorBg (default "black") Background color
     * @param string $style (default "normal") Style for the message (bold etc)
     * 
     * @return void
     */
    public static function displayMsg(
        $msg,
        $colorTxt = 'white',
        $colorBg = 'black',
        $style = 'normal'
    ) {
        if (func_num_args() === 1) {
            echo $msg;
            return;
        }
        
        //Gestion cas avec couleur
        $styleNum    = self::styleForShell($style);
        $colorTxtNum = self::colorForShell($colorTxt, 'txt');
        $colorBgNum  = self::colorForShell($colorBg, 'bg');

        echo "\033[".$styleNum.";".$colorBgNum.";".$colorTxtNum."m"
            .$msg
            ."\033[0m";
    }
    
    /**
     * Display a message in the console with a line break
     * 
     * @param string $msg Message to display
     * @param string $colorTxt (default "white") Text color
     * @param string $colorBg (default "black") Background color
     * @param string $style (default "normal") Style for the message (bold etc)
     * 
     * @return void
     */
    public static function displayMsgNL(
        $msg,
        $colorTxt = 'white',
        $colorBg = 'black',
        $style = 'normal'
    ) {
        if (func_num_args() === 1) {
            self::displayMsg($msg."\n");
            return;
        }
        
        self::displayMsg($msg."\n", $colorTxt, $colorBg, $style);
    }

    /**
     * Convert text color to shell value
     * 
     * @param string $color The humain color text
     * @param string $type ("txt"|"bg") If the color is for text of background
     * 
     * @return integer
     * 
     * @throws Exception If the color is not available
     */
    protected static function colorForShell($color, $type)
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

        if (!isset($colorList[$color])) {
            throw new Exception('Color '.$color.' is not available.');
        }

        //Text color
        if ($type === 'txt') {
            return $colorList[$color] + 30;
        }
        
        //Background color
        return $colorList[$color] + 40;
    }

    /**
     * Convert a humain style text to shell value
     * 
     * @param string $style The style value
     * 
     * @return integer
     * 
     * @throws Exception If the style is not available
     */
    protected static function styleForShell($style)
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

        if (!isset($styleList[$style])) {
            throw new Exception('Style '.$style.' is not available.');
        }

        return $styleList[$style];
    }
}
