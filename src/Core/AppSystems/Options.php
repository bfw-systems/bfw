<?php

namespace BFW\Core\AppSystems;

use BFW\Core\Options as CoreOptions;

class Options extends AbstractSystem
{
    /**
     * @var CoreOptions $options
     */
    protected $options;

    /**
     * Initialize option system with parameter passed to Application
     */
    public function __construct()
    {
        $this->options = new CoreOptions(
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
     * @return CoreOptions
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
