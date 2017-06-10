<?php

namespace BFW\Helpers\test\unit;

use \atoum;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Cli extends atoum
{
    /**
     * Test method for displayMsg()
     * 
     * @return void
     */
    public function testDisplayMsg()
    {
        $this->assert('test Cli::displayMsg with default parameters')
            ->output(function() {
                \BFW\Helpers\Cli::displayMsg('test displayMsg');
            })
                ->isEqualTo('test displayMsg');
        
        $this->assert('test Cli::displayMsg with a text color')
            ->output(function() {
                \BFW\Helpers\Cli::displayMsg('test displayMsg', 'red');
            })
                ->isEqualTo("\033[0;40;31mtest displayMsg\033[0m");
        
        $this->assert('test Cli::displayMsg with a text and background color')
            ->output(function() {
                \BFW\Helpers\Cli::displayMsg('test displayMsg', 'red', 'white');
            })
                ->isEqualTo("\033[0;47;31mtest displayMsg\033[0m");
        
        $this->assert('test Cli::displayMsg with a text and background color and with a text style')
            ->output(function() {
                \BFW\Helpers\Cli::displayMsg('test displayMsg', 'red', 'white', 'bold');
            })
                ->isEqualTo("\033[1;47;31mtest displayMsg\033[0m");
    }
    
    /**
     * Test method for displayMsgNL()
     * 
     * @return void
     */
    public function testDisplayMsgNL()
    {
        $this->assert('test Cli::displayMsgNL with default parameters')
            ->output(function() {
                \BFW\Helpers\Cli::displayMsgNL('test displayMsg');
            })
                ->isEqualTo('test displayMsg'."\n");
        
        $this->assert('test Cli::displayMsgNL with a text color')
            ->output(function() {
                \BFW\Helpers\Cli::displayMsgNL('test displayMsg', 'red');
            })
                ->isEqualTo("\033[0;40;31mtest displayMsg\n\033[0m");
        
        $this->assert('test Cli::displayMsgNL with a text and background color')
            ->output(function() {
                \BFW\Helpers\Cli::displayMsgNL('test displayMsg', 'red', 'white');
            })
                ->isEqualTo("\033[0;47;31mtest displayMsg\n\033[0m");
        
        $this->assert('test Cli::displayMsgNL with a text and background color and with a text style')
            ->output(function() {
                \BFW\Helpers\Cli::displayMsgNL('test displayMsg', 'red', 'white', 'bold');
            })
                ->isEqualTo("\033[1;47;31mtest displayMsg\n\033[0m");
    }
    
    /**
     * Test method for displayMsg() when it throw an exception
     * 
     * @return void
     */
    public function testDisplayException()
    {
        $this->assert('test Cli::displayMsg with a color exception')
            ->exception(function() {
                \BFW\Helpers\Cli::displayMsg('test displayMsg', 'rouge');
            })
                ->hasCode(\BFW\Helpers\Cli::ERR_COLOR_NOT_AVAILABLE)
                ->hasMessage('Color rouge is not available.');
        
        $this->assert('test Cli::displayMsg with a color exception')
            ->exception(function() {
                \BFW\Helpers\Cli::displayMsg('test displayMsg', 'red', 'white', 'gras');
            })
                ->hasCode(\BFW\Helpers\Cli::ERR_STYLE_NOT_AVAILABLE)
                ->hasMessage('Style gras is not available.');
    }
    
    /**
     * Test method for colorForShell()
     * 
     * @return void
     */
    public function testColorForShell()
    {
        $this->assert('test Cli::colorForShell for text color')
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('black', 'txt'))
                ->isEqualTo(30)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('red', 'txt'))
                ->isEqualTo(31)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('green', 'txt'))
                ->isEqualTo(32)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('yellow', 'txt'))
                ->isEqualTo(33)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('blue', 'txt'))
                ->isEqualTo(34)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('magenta', 'txt'))
                ->isEqualTo(35)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('cyan', 'txt'))
                ->isEqualTo(36)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('white', 'txt'))
                ->isEqualTo(37);
        
        $this->assert('test Cli::colorForShell for background color')
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('black', 'bg'))
                ->isEqualTo(40)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('red', 'bg'))
                ->isEqualTo(41)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('green', 'bg'))
                ->isEqualTo(42)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('yellow', 'bg'))
                ->isEqualTo(43)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('blue', 'bg'))
                ->isEqualTo(44)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('magenta', 'bg'))
                ->isEqualTo(45)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('cyan', 'bg'))
                ->isEqualTo(46)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callColorForShell('white', 'bg'))
                ->isEqualTo(47);
        
        $this->assert('test Cli::colorForShell with a unknown color for text')
            ->exception(function() {
                \BFW\Helpers\test\unit\mocks\Cli::callColorForShell('noir', 'txt');
            })
                ->hasCode(\BFW\Helpers\Cli::ERR_COLOR_NOT_AVAILABLE)
                ->hasMessage('Color noir is not available.');
        
        $this->assert('test Cli::colorForShell with a unknown color for background')
            ->exception(function() {
                \BFW\Helpers\test\unit\mocks\Cli::callColorForShell('noir', 'bg');
            })
                ->hasCode(\BFW\Helpers\Cli::ERR_COLOR_NOT_AVAILABLE)
                ->hasMessage('Color noir is not available.');
    }
    
    /**
     * Test method for styleForShell()
     * 
     * @return void
     */
    public function testStyleForShell()
    {
        $this->assert('test Cli::styleForShell for text color')
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callStyleForShell('normal'))
                ->isEqualTo(0)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callStyleForShell('bold'))
                ->isEqualTo(1)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callStyleForShell('not-bold'))
                ->isEqualTo(21)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callStyleForShell('underline'))
                ->isEqualTo(4)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callStyleForShell('not-underline'))
                ->isEqualTo(24)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callStyleForShell('blink'))
                ->isEqualTo(5)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callStyleForShell('not-blink'))
                ->isEqualTo(25)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callStyleForShell('reverse'))
                ->isEqualTo(7)
            ->integer(\BFW\Helpers\test\unit\mocks\Cli::callStyleForShell('not-reverse'))
                ->isEqualTo(27);
        
        $this->assert('test Cli::styleForShell with a unknown style')
            ->exception(function() {
                \BFW\Helpers\test\unit\mocks\Cli::callStyleForShell('gras');
            })
                ->hasCode(\BFW\Helpers\Cli::ERR_STYLE_NOT_AVAILABLE)
                ->hasMessage('Style gras is not available.');
    }
}
