<?php

namespace BFW\Core\AppSystems;

class Options extends AbstractSystem
{
    /**
     * @var \BFW\Core\Options|null $options
     */
    protected $options;
    
    /**
     * {@inheritdoc}
     * 
     * @return \BFW\Core\Options|null
     */
    public function __invoke()
    {
        return $this->options;
    }
    
    /**
     * Getter accessor to property options
     * 
     * @return \BFW\Core\Options|null
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * {@inheritdoc}
     * Initialize option system with parameter passed to Application
     */
    public function init()
    {
        $this->options = new \BFW\Core\Options(
            $this->obtainDefaultOptions(),
            \BFW\Application::getInstance()->getDeclaredOptions()
        );
        
        $this->options
            ->searchPaths()
            ->checkPaths()
        ;
        
        $this->initStatus = true;
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
