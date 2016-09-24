<?php

namespace BFW\Helpers\test\unit\mocks;

class Cli extends \BFW\Helpers\Cli
{
    public static function callColorForShell($color, $type)
    {
        return self::colorForShell($color, $type);
    }
    
    public static function callStyleForShell($style)
    {
        return self::styleForShell($style);
    }
}
