<?php

namespace BFW;

use \Exception;

/**
 * Class to manage Options
 */
class Options
{
    /**
     * @const ERR_KEY_NOT_EXIST Exception code if a key not exist.
     */
    const ERR_KEY_NOT_EXIST = 1312001;
    
    /**
     * @var array $option option's list
     */
    protected $options = [];

    /**
     * Constructor
     * Merge default option with passed values
     * 
     * @param array $defaultOptions Default options
     * @param array $options Options from applications/users
     */
    public function __construct($defaultOptions, $options)
    {
        $this->options = array_merge($defaultOptions, $options);
    }
    
    /**
     * Getter accessor to options property
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get the value for an option
     * 
     * @param string $optionKey The option key
     * 
     * @return mixed
     * 
     * @throws Exception If the key not exists
     */
    public function getValue($optionKey)
    {
        if (!isset($this->options[$optionKey])) {
            throw new Exception(
                'Option key '.$optionKey.' not exist.',
                $this::ERR_KEY_NOT_EXIST
            );
        }

        return $this->options[$optionKey];
    }
}
