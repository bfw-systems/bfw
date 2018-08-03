<?php

namespace BFW\Core\AppSystems;

class Session extends AbstractSystem
{
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
     * Initialize sessions system
     * Automaticaly destroy cookie if browser quit and start sessions
     * 
     * @return void
     */
    public function init()
    {
        if ($this->obtainRunSession() === false) {
            $this->initStatus = true;
            return;
        }

        //Destroy session cookie if browser quit
        session_set_cookie_params(0);

        //Run session
        session_start();
        
        $this->initStatus = true;
    }
    
    /**
     * Obtain the value of the option runSession passed to Application
     * 
     * @return boolean
     */
    protected function obtainRunSession()
    {
        return \BFW\Application::getInstance()
            ->getOptions()
            ->getValue('runSession')
        ;
    }
}
