<?php

namespace BFW\Core\AppSystems;

class Session extends AbstractSystem
{
    /**
     * Initialize sessions system
     * Automaticaly destroy cookie if browser quit and start sessions
     * 
     * @return void
     */
    public function __construct()
    {
        if ($this->obtainRunSession() === false) {
            return;
        }

        //Destroy session cookie if browser quit
        session_set_cookie_params(0);

        //Run session
        session_start();
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return null
     */
    public function __invoke()
    {
        return null;
    }
    
    /**
     * Obtain the value of the option runSession passed to Application
     * 
     * @return boolean
     */
    protected function obtainRunSession(): bool
    {
        return \BFW\Application::getInstance()
            ->getOptions()
            ->getValue('runSession')
        ;
    }
}
