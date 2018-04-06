<?php

namespace BFW\Helpers\Test\Mock;

/**
 * Mock for Helpers\Cli class
 */
class Cli extends \BFW\Helpers\Cli
{
    /**
     * Call the protected method colorForShell
     * @see \BFW\Helpers\Cli::colorForShell()
     */
    public static function colorForShell($color, $type)
    {
        return parent::colorForShell($color, $type);
    }

    /**
     * Call the protected method styleForShell
     * @see \BFW\Helpers\Cli::styleForShell()
     */
    public static function styleForShell($style)
    {
        return parent::styleForShell($style);
    }
}
