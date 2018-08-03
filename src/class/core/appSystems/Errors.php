<?php

namespace BFW\Core\AppSystems;

class Errors extends AbstractSystem
{
    /**
     * @var \BFW\Core\Errors|null $errors The error object
     */
    protected $errors;
    
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        return $this->errors;
    }

    /**
     * Getter accessor to property errors
     * 
     * @return \BFW\Core\Errors|null
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * {@inheritdoc}
     * Initialize the errors property
     */
    public function init()
    {
        $this->errors     = new \BFW\Core\Errors;
        $this->initStatus = true;
    }
}
