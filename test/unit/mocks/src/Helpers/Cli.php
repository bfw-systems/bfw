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
    public static function colorForShell(string $color, string $type): int
    {
        return parent::colorForShell($color, $type);
    }

    /**
     * Call the protected method styleForShell
     * @see \BFW\Helpers\Cli::styleForShell()
     */
    public static function styleForShell(string $style): int
    {
        return parent::styleForShell($style);
    }
}
