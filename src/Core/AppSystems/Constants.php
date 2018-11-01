<?php

namespace BFW\Core\AppSystems;

class Constants extends AbstractSystem
{
    /**
     * Define all constants
     */
    public function __construct()
    {
        \BFW\Helpers\Constants::create('ROOT_DIR', $this->obtainRootDir());

        \BFW\Helpers\Constants::create('APP_DIR', ROOT_DIR.'app/');
        \BFW\Helpers\Constants::create('SRC_DIR', ROOT_DIR.'src/');
        \BFW\Helpers\Constants::create('WEB_DIR', ROOT_DIR.'web/');

        \BFW\Helpers\Constants::create('CONFIG_DIR', APP_DIR.'config/');
        \BFW\Helpers\Constants::create('MODULES_DIR', APP_DIR.'modules/');

        \BFW\Helpers\Constants::create('CLI_DIR', SRC_DIR.'cli/');
        \BFW\Helpers\Constants::create('CTRL_DIR', SRC_DIR.'controllers/');
        \BFW\Helpers\Constants::create('MODELES_DIR', SRC_DIR.'modeles/');
        \BFW\Helpers\Constants::create('VIEW_DIR', SRC_DIR.'view/');
    }
    
    /**
     * {@inheritdoc}
     * @return null
     */
    public function __invoke()
    {
        return null;
    }
    
    /**
     * Obtain the path of the application root directory
     * 
     * @return string
     */
    protected function obtainRootDir(): string
    {
        return \BFW\Application::getInstance()
            ->getOptions()
            ->getValue('rootDir')
        ;
    }
}
