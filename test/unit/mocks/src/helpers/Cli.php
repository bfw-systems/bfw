<?php

namespace BFW\Helpers\test\unit\mocks;

/**
 * Mock for Helpers\Cli class
 */
class Cli extends \BFW\Helpers\Cli
{
    /**
     * Call the protected method colorForShell
     * @see \BFW\Helpers\Cli::colorForShell()
     */
    public static function callColorForShell($color, $type)
    {
        return self::colorForShell($color, $type);
    }
    
    /**
     * Call the protected method styleForShell
     * @see \BFW\Helpers\Cli::styleForShell()
     */
    public static function callStyleForShell($style)
    {
        return self::styleForShell($style);
    }
}
