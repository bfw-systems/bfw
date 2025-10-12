<?php

namespace BFW\Core\AppSystems;

use BFW\Config as BFWConfig;

class Config extends AbstractSystem
{
    /**
     * @var BFWConfig $config The config object for BFW framework
     */
    protected $config;

    /**
     * Define config object and load all config file used by the framework
     */
    public function __construct()
    {
        $this->config = new BFWConfig('bfw');
        $this->config->loadFiles();
    }

    /**
     * {@inheritdoc}
     *
     * @return BFWConfig
     */
    public function __invoke()
    {
        return $this->config;
    }

    /**
     * Getter accessor to the property config
     *
     * @return \BFW\Config
     */
    public function getConfig()
    {
        return $this->config;
    }
}
