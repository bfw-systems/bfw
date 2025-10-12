<?php

namespace BFW\Core\AppSystems;

use BFW\Core\Errors as CoreErrors;

class Errors extends AbstractSystem
{
    /**
     * @var CoreErrors $errors The error object
     */
    protected $errors;

    /**
     * Initialize the errors property
     */
    public function __construct()
    {
        $this->errors = new CoreErrors();
    }

    /**
     * {@inheritdoc}
     *
     * @return CoreErrors
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
