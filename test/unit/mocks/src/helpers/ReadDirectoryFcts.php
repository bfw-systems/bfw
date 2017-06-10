<?php
/**
 * Mocks for native functions
 * Use by test for class who extended ReadDirectory class
 * and have a different namespace
 */

namespace BFW\Helpers;

/**
 * Class to override native PHP function used by ReadDirectory
 */
class mockReadDirectoryNativeFct
{
    /**
     * Singleton pattern
     * @var \BFW\Helpers\mockReadDirectoryNativeFct $instance
     */
    protected static $instance;
    
    /**
     * @var array $fctOverrided List of function overrided and the value to
     * return. Call native function if value is null.
     */
    protected $fctOverrided;
    
    /**
     * Constructor (protected because Singleton pattern)
     */
    protected function __construct()
    {
        $this->fctOverrided = [
            'opendir'  => null,
            'readdir'  => null,
            'is_dir'   => null,
            'closedir' => null
        ];
    }
    
    /**
     * Generate the instance or get the current instance (Singleton pattern)
     * 
     * @return \BFW\Helpers\mockReadDirectoryNativeFct
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }
    
    /**
     * Getter access to property fctOverrided
     * 
     * @return array
     */
    public function getFctOverrided()
    {
        return $this->fctOverrided;
    }
    
    /**
     * Setter access to property fctOverrided
     * 
     * @param array $fctOverrided The new value of the property
     * 
     * @return void
     */
    public function setFctOverrided($fctOverrided)
    {
        $this->fctOverrided = $fctOverrided;
    }
    
    /**
     * Call a overrided function.
     * If the value of the override is null, call the native function
     * 
     * @param string $fctName The function name
     * @param mixed ...$params Parameter passed to the native function
     * 
     * @return mixed
     */
    public function callFct($fctName, ...$params)
    {
        if ($this->fctOverrided[$fctName] === null) {
            $calledFct = '\\'.$fctName;
            return $calledFct(...$params);
        }

        if (is_callable($this->fctOverrided[$fctName])) {
            return $this->fctOverrided[$fctName](...$params);
        }

        return $this->fctOverrided[$fctName];
    }
}

/**
 * Override the native opendir function.
 * {@inheritdoc}
 */
function opendir(...$params)
{
    $mock = mockReadDirectoryNativeFct::getInstance();
    return $mock->callFct('opendir', ...$params);
}

/**
 * Override the native readdir function.
 * {@inheritdoc}
 */
function readdir(...$params)
{
    $mock = mockReadDirectoryNativeFct::getInstance();
    return $mock->callFct('readdir', ...$params);
}

/**
 * Override the native is_dir function.
 * {@inheritdoc}
 */
function is_dir(...$params)
{
    $mock = mockReadDirectoryNativeFct::getInstance();
    return $mock->callFct('is_dir', ...$params);
}

/**
 * Override the native closedir function.
 * {@inheritdoc}
 */
function closedir(...$params)
{
    $mock = mockReadDirectoryNativeFct::getInstance();
    return $mock->callFct('closedir', ...$params);
}
