<?php

namespace BFW\Helpers;

use \Exception;

/**
 * Helpers for cli applications
 */
class Cli
{
    /**
     * @const ERR_COLOR_NOT_AVAILABLE Exception code if the color is not
     * available.
     */
    const ERR_COLOR_NOT_AVAILABLE = 1201001;
    
    /**
     * @const ERR_STYLE_NOT_AVAILABLE Exception code if the style is not
     * available.
     */
    const ERR_STYLE_NOT_AVAILABLE = 1201002;
    
    /**
     * @const FLUSH_AUTO Value to use on $flushMethod property to automaticaly
     * call the method ob_flush into function displayMsg and displayMsgNoNL
     */
    const FLUSH_AUTO = 'auto';
    
    /**
     * @const FLUSH_MANUAL Value to use on $flushMethod property to NOT
     * automaticaly call the method ob_flush into function displayMsg
     * and displayMsgNL
     */
    const FLUSH_MANUAL = 'manual';
    
    /**
     * @var string $callObFlush (default: self::FLUSH_AUTO) Define if the
     * method ob_flush is called or not into the method displayMsg
     * and displayMsgNL
     */
    public static $callObFlush = self::FLUSH_AUTO;
    
    /**
     * Display a message in the console without a line break
     * If only the first parameter is passed, the colors will be those
     * currently used in the console
     * 
     * @param string $msg Message to display
     * @param string $colorTxt (default "white") Text color
     * @param string $style (default "normal") Style for the message (bold etc)
     * @param string $colorBg (default "black") Background color
     * 
     * @return void
     */
    public static function displayMsg(
        $msg,
        $colorTxt = 'white',
        $style = 'normal',
        $colorBg = 'black'
    ) {
        if (func_num_args() === 1) {
            echo $msg;
            
            if (self::$callObFlush === self::FLUSH_AUTO) {
                ob_flush();
            }
            
            return;
        }
        
        //Get colors values
        $styleNum    = self::styleForShell($style);
        $colorTxtNum = self::colorForShell($colorTxt, 'txt');
        $colorBgNum  = self::colorForShell($colorBg, 'bg');

        echo "\033[".$styleNum.";".$colorBgNum.";".$colorTxtNum."m"
            .$msg
            ."\033[0m";
        
        if (self::$callObFlush === self::FLUSH_AUTO) {
            ob_flush();
        }
    }
    
    /**
     * Display a message in the console with a line break
     * If only the first parameter is passed, the colors will be those
     * currently used in the console
     * 
     * @param string $msg Message to display
     * @param string $colorTxt (default "white") Text color
     * @param string $style (default "normal") Style for the message (bold etc)
     * @param string $colorBg (default "black") Background color
     * 
     * @return void
     */
    public static function displayMsgNL(
        $msg,
        $colorTxt = 'white',
        $style = 'normal',
        $colorBg = 'black'
    ) {
        $nbArgs       = func_num_args();
        $currentClass = get_called_class();
        
        if ($nbArgs === 1) {
            $currentClass::displayMsg($msg."\n");
        } elseif ($nbArgs > 3) {
            $currentClass::displayMsg($msg."\n", $colorTxt, $style, $colorBg);
        } else {
            $currentClass::displayMsg($msg."\n", $colorTxt, $style);
        }
    }

    /**
     * Convert text color to shell value
     * 
     * @param string $color The human color text
     * @param string $type ("txt"|"bg") If the color is for text or background
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
            throw new Exception(
                'Color '.$color.' is not available.',
                self::ERR_COLOR_NOT_AVAILABLE
            );
        }

        //Text color
        if ($type === 'txt') {
            return $colorList[$color] + 30;
        }
        
        //Background color
        return $colorList[$color] + 40;
    }

    /**
     * Convert a human style text to shell value
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
            throw new Exception(
                'Style '.$style.' is not available.',
                self::ERR_STYLE_NOT_AVAILABLE
            );
        }

        return $styleList[$style];
    }
}
