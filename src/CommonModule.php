<?php

namespace BFW;

/**
 * Common base class for BFW modules
 * 
 * This class provides a foundation for module authors to create type-safe modules
 * that work with PHP 8.2+ without dynamic property deprecation warnings.
 * 
 * Module authors can extend this class and declare their properties properly,
 * gaining full IDE support and type safety.
 */
abstract class CommonModule implements ModuleInterface
{
    /**
     * @var string|null $moduleName The name of the module instance
     */
    protected ?string $moduleName = null;

    /**
     * @var \BFW\Config|null $config Config object for this module
     */
    protected ?\BFW\Config $config = null;

    /**
     * Set the module name (called by the framework)
     * 
     * @param string $name The module name
     * @return self
     */
    public function setModuleName(string $name): self
    {
        $this->moduleName = $name;
        return $this;
    }

    /**
     * Get the module name
     * 
     * @return string|null
     */
    public function getModuleName(): ?string
    {
        return $this->moduleName;
    }

    /**
     * Set the config object (called by the framework)
     * 
     * @param \BFW\Config|null $config The config object
     * @return self
     */
    public function setConfig(?\BFW\Config $config): self
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Get the config object
     * 
     * @return \BFW\Config|null
     */
    public function getConfig(): ?\BFW\Config
    {
        return $this->config;
    }

    /**
     * Execute the module's main functionality
     * 
     * This method must be implemented by concrete module classes.
     * 
     * @return void
     */
    abstract public function run(): void;
}