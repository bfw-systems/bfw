<?php

namespace BFW\Core\AppSystems;

class Errors extends AbstractSystem
{
    /**
     * @var \BFW\Core\Errors $errors The error object
     */
    protected $errors;
    
    /**
     * Initialize the errors property
     */
    public function __construct()
    {
        $this->errors = new \BFW\Core\Errors;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Core\Errors
     */
    public function __invoke()
    {
        return $this->errors;
    }

    /**
     * Getter accessor to property errors
     * 
     * @return \BFW\Core\Errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
