<?php

namespace BFW\Helpers\test\unit;

use \atoum;
use \BFW\test\helpers\ApplicationInit as AppInit;

require_once(__DIR__.'/../../../../vendor/autoload.php');

class Cli extends atoum
{
    /**
     * @var \BFW\test\helpers\ApplicationInit $app BFW Application instance
     */
    protected $app;
    
    /**
     * @var string $lastFlushedMsg The last output sent with ob_flush function
     */
    protected $lastFlushedMsg = '';
    
    /**
     * Call before each test method
     * Instantiate BFW Application class : call ob_start()
     * 
     * @param $testMethod string The name of the test method executed
     * 
     * @return void
     */
    public function beforeTestMethod($testMethod)
    {
        $this->app = AppInit::init();
        
        //Mock php function ob_flush.
        //Because there is conflict with output atoum asserter: it not catch
        //output sent with ob_flush function into tested class because it not
        //the same ob_start.
        $this->function->ob_flush = function() {
            $this->lastFlushedMsg = ob_get_contents();
            ob_clean();
        };
    }
    
    public function testConstants()
    {
        $this->assert('test Cli::FLUSH_AUTO value')
            ->string(\BFW\Helpers\Cli::FLUSH_AUTO)
                ->isEqualTo('auto');
        
        $this->assert('test Cli::FLUSH_MANUAL value')
            ->string(\BFW\Helpers\Cli::FLUSH_MANUAL)
                ->isEqualTo('manual');
    }
    
    /**
     * Test method for displayMsg() with flush to auto
     * 
     * @return void
     */
    public function testDisplayMsgWithFlush()
    {
        $this->assert('test Cli::$callObFlush default value')
            ->variable(\BFW\Helpers\Cli::$callObFlush)
                ->isEqualTo(\BFW\Helpers\Cli::FLUSH_AUTO);
        
        $this->assert('test Cli::displayMsg with default parameters')
            ->if(\BFW\Helpers\Cli::displayMsg('test displayMsg'))
            ->string($this->lastFlushedMsg)
                ->isEqualTo('test displayMsg');
        
        $this->assert('test Cli::displayMsg with a text color')
            ->if(\BFW\Helpers\Cli::displayMsg('test displayMsg', 'red'))
            ->string($this->lastFlushedMsg)
                ->isEqualTo("\033[0;40;31mtest displayMsg\033[0m");
        
        $this->assert('test Cli::displayMsg with a text and background color')
            ->if(\BFW\Helpers\Cli::displayMsg('test displayMsg', 'red', 'white'))
            ->string($this->lastFlushedMsg)
                ->isEqualTo("\033[0;47;31mtest displayMsg\033[0m");
        
        $this->assert('test Cli::displayMsg with a text and background color and with a text style')
            ->if(\BFW\Helpers\Cli::displayMsg('test displayMsg', 'red', 'white', 'bold'))
            ->string($this->lastFlushedMsg)
                ->isEqualTo("\033[1;47;31mtest displayMsg\033[0m");
    }
    
    /**
     * Test method for displayMsg() with flush to auto
     * 
     * @return void
     */
    public function testDisplayMsgWithoutFlush()
    {
        $this->assert('set Cli::$callObFlush value to manual')
            ->given(\BFW\Helpers\Cli::$callObFlush = \BFW\Helpers\Cli::FLUSH_MANUAL);
        
        $this->assert('test Cli::displayMsg without flush')
            ->if(\BFW\Helpers\Cli::displayMsg('test displayMsg - '))
            ->and(\BFW\Helpers\Cli::displayMsg('test displayMsg - ', 'red'))
            ->and(\BFW\Helpers\Cli::displayMsg('test displayMsg - ', 'red', 'white'))
            ->and(\BFW\Helpers\Cli::displayMsg('test displayMsg', 'red', 'white', 'bold'))
            ->then
            ->given($output = ob_get_contents())
            ->and(ob_clean())
            ->string($output)
                ->isEqualTo(
                    'test displayMsg - '
                    ."\033[0;40;31mtest displayMsg - \033[0m"
                    ."\033[0;47;31mtest displayMsg - \033[0m"
                    ."\033[1;47;31mtest displayMsg\033[0m"
                );
    }
    
    /**
     * Test method for displayMsgNL()
     * 
     * @return void
     */
    public function testDisplayMsgNL()
    {
        $this->assert('test Cli::$callObFlush default value')
            ->variable(\BFW\Helpers\Cli::$callObFlush)
                ->isEqualTo(\BFW\Helpers\Cli::FLUSH_AUTO);
        
        $this->assert('test Cli::displayMsgNL with default parameters')
            ->if(\BFW\Helpers\Cli::displayMsgNL('test displayMsg'))
            ->string($this->lastFlushedMsg)
                ->isEqualTo('test displayMsg'."\n");
        
        $this->assert('test Cli::displayMsgNL with a text color')
            ->if(\BFW\Helpers\Cli::displayMsgNL('test displayMsg', 'red'))
            ->string($this->lastFlushedMsg)
                ->isEqualTo("\033[0;40;31mtest displayMsg\n\033[0m");
        
        $this->assert('test Cli::displayMsgNL with a text and background color')
            ->if(\BFW\Helpers\Cli::displayMsgNL('test displayMsg', 'red', 'white'))
            ->string($this->lastFlushedMsg)
                ->isEqualTo("\033[0;47;31mtest displayMsg\n\033[0m");
        
        $this->assert('test Cli::displayMsgNL with a text and background color and with a text style')
            ->if(\BFW\Helpers\Cli::displayMsgNL('test displayMsg', 'red', 'white', 'bold'))
            ->string($this->lastFlushedMsg)
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
