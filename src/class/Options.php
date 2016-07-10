<?php

namespace BFW;

use \Exception;

class Options
{
    protected $defaultOptions = [];
    protected $options = [];
    
    public function __construct($defaultOptions, $options)
    {
        $this->options = array_merge($defaultOptions, $options);
    }
    
    public function getOption($optionKey)
    {
        if(!isset($this->options[$optionKey])) {
            throw new Exception('Option key '.$optionKey.' not exist.');
        }
        
        return $this->options[$optionKey];
    }
}
