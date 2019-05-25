<?php

namespace BFW\Core\AppSystems;

class ComposerLoader extends AbstractSystem
{
    /**
     * @var \Composer\Autoload\ClassLoader $loader Composer auto-loader
     */
    protected $loader;
    
    /**
     * Define loader property and add all namespaces
     */
    public function __construct()
    {
        $this->loader = require(
            $this->obtainVendorDir().'autoload.php'
        );

        $this->addComposerNamespaces();
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return \Composer\Autoload\ClassLoader
     */
    public function __invoke()
    {
        return $this->loader;
    }
    
    /**
     * Getter accessor to property loader
     * 
     * @return \Composer\Autoload\ClassLoader
     */
    public function getLoader()
    {
        return $this->loader;
    }
    
    /**
     * Return the path to the vendor directory
     * 
     * @return string
     */
    protected function obtainVendorDir(): string
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
        $this->loader->addPsr4('Modules\\', MODULES_ENABLED_DIR);
    }
}
