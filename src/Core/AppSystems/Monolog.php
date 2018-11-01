<?php

namespace BFW\Core\AppSystems;

class Monolog extends AbstractSystem
{
    /**
     * @var \BFW\Monolog $monolog The monolog system for bfw channel
     */
    protected $monolog;
    
    /**
     * Initialize all monolog handlers declared for bfw channel
     */
    public function __construct()
    {
        $config        = \BFW\Application::getInstance()->getConfig();
        $this->monolog = new \BFW\Monolog('bfw', $config);
        $this->monolog->addAllHandlers('handlers', 'monolog.php');
        
        $this->monolog->getLogger()->debug(
            'Currently during the initialization framework step.'
        );
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Monolog
     */
    public function __invoke()
    {
        return $this->monolog;
    }

    /**
     * @return \BFW\Monolog
     */
    public function getMonolog()
    {
        return $this->monolog;
    }
}
