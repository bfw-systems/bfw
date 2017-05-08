<?php

namespace BFW;

use \Exception;

/**
 * Class to manage Options
 */
class Options
{
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
     * Get the value for an option
     * 
     * @param string $optionKey The option key
     * 
     * @return mixed
     * 
     * @throws Exception If the key not exists
     */
    public function getOption($optionKey)
    {
        if (!isset($this->options[$optionKey])) {
            throw new Exception('Option key '.$optionKey.' not exist.');
        }

        return $this->options[$optionKey];
    }
}
