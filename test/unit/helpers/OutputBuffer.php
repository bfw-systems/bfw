<?php

namespace BFW\Test\Helpers;

trait OutputBuffer
{
    /**
     * Define an output buffer to get the message sent to buffer.
     * It's because the output asserter into atoum have a conflict with
     * bfw output buffer.
     * 
     * @param string $lastFlushedMsg The variable where the buffer content
     * will be save
     * 
     * @return void
     */
    protected function defineOutputBuffer(&$lastFlushedMsg)
    {
        $lastFlushedMsg = '';
        ob_start(function($buffer) use (&$lastFlushedMsg) {
            $lastFlushedMsg .= $buffer;
            return '';
        });
    }
}
