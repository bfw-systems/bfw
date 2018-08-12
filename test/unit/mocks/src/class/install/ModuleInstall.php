<?php

namespace BFW\Install\Test\Mock;

class ModuleInstall extends \BFW\Install\ModuleInstall
{
    /**
     * Setter to parent property projectPath
     * 
     * @param string $projectPath
     * 
     * @return $this
     */
    public function setProjectPath(string $projectPath): self
    {
        $this->projectPath = $projectPath;
        return $this;
    }

    /**
     * Setter to parent property forceReinstall
     * 
     * @param boolean $forceReinstall
     * 
     * @return $this
     */
    public function setForceReinstall(bool $forceReinstall): self
    {
        $this->forceReinstall = $forceReinstall;
        return $this;
    }

    /**
     * Setter to parent property name
     * 
     * @param string $name
     * 
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Setter to parent property sourcePath
     * 
     * @param string $sourcePath
     * 
     * @return $this
     */
    public function setSourcePath(string $sourcePath): self
    {
        $this->sourcePath = $sourcePath;
        return $this;
    }

    /**
     * Setter to parent property sourceSrcPath
     * 
     * @param string $sourceSrcPath
     * 
     * @return $this
     */
    public function setSourceSrcPath(string $sourceSrcPath): self
    {
        $this->sourceSrcPath = $sourceSrcPath;
        return $this;
    }

    /**
     * Setter to parent property sourceConfigPath
     * 
     * @param string $sourceConfigPath
     * 
     * @return $this
     */
    public function setSourceConfigPath(string $sourceConfigPath): self
    {
        $this->sourceConfigPath = $sourceConfigPath;
        return $this;
    }

    /**
     * Setter to parent property configFilesList
     * 
     * @param array $configFilesList
     * 
     * @return $this
     */
    public function setConfigFilesList(array $configFilesList): self
    {
        $this->configFilesList = $configFilesList;
        return $this;
    }

    /**
     * Setter to parent property sourceInstallScript
     * 
     * @param string|bool $sourceInstallScript
     * 
     * @return $this
     */
    public function setSourceInstallScript($sourceInstallScript): self
    {
        $this->sourceInstallScript = $sourceInstallScript;
        return $this;
    }

    /**
     * Setter to parent property targetSrcPath
     * 
     * @param string $targetSrcPath
     * 
     * @return $this
     */
    public function setTargetSrcPath(string $targetSrcPath): self
    {
        $this->targetSrcPath = $targetSrcPath;
        return $this;
    }

    /**
     * Setter to parent property targetConfigPath
     * 
     * @param string $targetConfigPath
     * 
     * @return $this
     */
    public function setTargetConfigPath(string $targetConfigPath): self
    {
        $this->targetConfigPath = $targetConfigPath;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * Use the method installInfos from mocked Module class.
     */
    protected function obtainInfosFromModule(): \stdClass
    {
        return \BFW\Test\Mock\Module::installInfos($this->sourcePath);
    }
}
