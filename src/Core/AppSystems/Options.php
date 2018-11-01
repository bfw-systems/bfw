<?php

namespace BFW\Core\AppSystems;

class Options extends AbstractSystem
{
    /**
     * @var \BFW\Core\Options $options
     */
    protected $options;
    
    /**
     * Initialize option system with parameter passed to Application
     */
    public function __construct()
    {
        $this->options = new \BFW\Core\Options(
            $this->obtainDefaultOptions(),
            \BFW\Application::getInstance()->getDeclaredOptions()
        );
        
        $this->options
            ->searchPaths()
            ->checkPaths()
        ;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Core\Options
     */
    public function __invoke()
    {
        return $this->options;
    }
    
    /**
     * Getter accessor to property options
     * 
     * @return \BFW\Core\Options
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * Define default option values
     * 
     * @return array
     */
    protected function obtainDefaultOptions(): array
    {
        return [
            'rootDir'    => null,
            'vendorDir'  => null,
            'runSession' => true
        ];
    }
}
