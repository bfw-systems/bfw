<?php

namespace BFW\Test\Helpers;

/**
 * Class used by \Core\Errors unit test to test with a personal render class
 */
class ErrorsRenderClass
{
    /**
     * @var \BFW\Test\Helpers\ErrorsRenderClass $instance : Singleton pattern
     */
    protected static $instance = null;
    
    /**
     * @var null|string $errType
     */
    public $errType;
    
    /**
     * @var null|string $errMsg
     */
    public $errMsg;
    
    /**
     * @var null|string $errFile
     */
    public $errFile;
    
    /**
     * @var null|string $errLine
     */
    public $errLine;
    
    /**
     * @var null|array $backtrace
     */
    public $backtrace;
    
    /**
     * Singleton pattern (protected constructor)
     */
    protected function __construct()
    {
        //Nothing to do.
    }
    
    /**
     * Create the singleton instance, or return the instance.
     * 
     * @return \BFW\Test\Helpers\ErrorsRenderClass
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            $class          = get_called_class();
            self::$instance = new $class;
        }
        
        return self::$instance;
    }
    
    /**
     * Populate attributes
     * 
     * @param string $errType
     * @param string $errMsg
     * @param string $errFile
     * @param string $errLine
     * @param array $backtrace
     * 
     * @return void
     */
    public static function render(
        $errType,
        $errMsg,
        $errFile,
        $errLine,
        $backtrace
    ) {
        $self = ErrorsRenderClass::getInstance();
        
        $self->errType   = $errType;
        $self->errMsg    = $errMsg;
        $self->errFile   = $errFile;
        $self->errLine   = $errLine;
        $self->backtrace = $backtrace;
    }
}
