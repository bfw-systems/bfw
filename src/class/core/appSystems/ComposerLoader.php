<?php

namespace BFW\Core\AppSystems;

class ComposerLoader extends AbstractSystem
{
    /**
     * @var \Composer\Autoload\ClassLoader|null $loader Composer auto-loader
     */
    protected $loader;
    
    /**
     * {@inheritdoc}
     * 
     * @return \Composer\Autoload\ClassLoader|null
     */
    public function __invoke()
    {
        return $this->loader;
    }
    
    /**
     * Getter accessor to property loader
     * 
     * @return \Composer\Autoload\ClassLoader|null
     */
    public function getLoader()
    {
        return $this->loader;
    }
    
    /**
     * {@inheritdoc}
     * Define loader property and add all namespaces
     */
    public function init()
    {
        $this->loader = require(
            $this->obtainVendorDir().'autoload.php'
        );
        
        $this->addComposerNamespaces();
        
        $this->initStatus = true;
    }
    
    /**
     * Return the path to the vendor directory
     * 
     * @return string
     */
    protected function obtainVendorDir()
    {
        return \BFW\Application::getInstance()
            ->getOptions()
            ->getValue('vendorDir')
        ;
    }

    /**
     * Add namespaces used by a BFW Application to composer
     * 
     * @return void
     */
    protected function addComposerNamespaces()
    {
        $this->loader->addPsr4('Controller\\', CTRL_DIR);
        $this->loader->addPsr4('Modules\\', MODULES_DIR);
        $this->loader->addPsr4('Modeles\\', MODELES_DIR);
    }
}
