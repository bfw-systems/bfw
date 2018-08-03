<?php

namespace BFW\Core\AppSystems;

class Monolog extends AbstractSystem
{
    /**
     * @var \BFW\Monolog|null $monolog The monolog system for bfw channel
     */
    protected $monolog;
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Monolog|null
     */
    public function __invoke()
    {
        return $this->monolog;
    }

    /**
     * @return \BFW\Monolog|null
     */
    public function getMonolog()
    {
        return $this->monolog;
    }
    
    /**
     * {@inheritdoc}
     * Initialize all monolog handlers declared for bfw channel
     */
    public function init()
    {
        $config        = \BFW\Application::getInstance()->getConfig();
        $this->monolog = new \BFW\Monolog('bfw', $config);
        $this->monolog->addAllHandlers('handlers', 'monolog.php');
        
        $this->monolog->getLogger()->debug(
            'Currently during the initialization framework step.'
        );
        
        $this->initStatus = true;
    }
}
