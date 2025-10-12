<?php

namespace BFW\Core\AppSystems;

use BFW\Monolog as BFWMonolog;

class Monolog extends AbstractSystem
{
    /**
     * @var BFWMonolog $monolog The monolog system for bfw channel
     */
    protected $monolog;

    /**
     * Initialize all monolog handlers declared for bfw channel
     */
    public function __construct()
    {
        $config        = \BFW\Application::getInstance()->getConfig();
        $this->monolog = new BFWMonolog('bfw', $config);
        $this->monolog->addAllHandlers('handlers', 'monolog.php');

        $this->monolog->getLogger()->debug(
            'Currently during the initialization framework step.'
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return BFWMonolog
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
