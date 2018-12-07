<?php

namespace BFW\Test\Mock\Core;

Class Cli extends \BFW\Core\Cli
{
    /**
     * @var string $fileInArg to mock the usage of getopt function to get
     * the file to execute
     */
    protected $fileInArg = '';
    
    /**
     * @var boolean $useArgToObtainFile To determine if the system use the
     * property $fileInArg (false) or normal usage with getopt (true)
     */
    protected $useArgToObtainFile = true;
    
    /**
     * @var boolean $isExecuted To determine if the methode execFile have
     * been call.
     */
    protected $isExecuted = false;
    
    /**
     * Setter to parent property executedFile
     * 
     * @param string $executedFile
     * 
     * @return $this
     */
    public function setExecutedFile(string $executedFile): self
    {
        $this->executedFile = $executedFile;
        return $this;
    }
    
    /**
     * Getter to property fileInArg
     * 
     * @return string
     */
    public function getFileInArg(): string 
    {
        return $this->fileInArg;
    }

    /**
     * Getter to property useArgToObtainFile
     * 
     * @return boolean
     */
    public function getUseArgToObtainFile(): bool
    {
        return $this->useArgToObtainFile;
    }
    
    /**
     * Getter to property isExecuted
     * 
     * @return boolean
     */
    public function getIsExecuted(): bool
    {
        return $this->isExecuted;
    }

    /**
     * Setter to property fileInArg
     * 
     * @param string $fileInArg
     * 
     * @return $this
     */
    public function setFileInArg(string $fileInArg): self
    {
        $this->fileInArg = $fileInArg;
        return $this;
    }

    /**
     * Setter to property useArgToObtainFile
     * 
     * @param boolean $useArgToObtainFile
     * 
     * @return $this
     */
    public function setUseArgToObtainFile(bool $useArgToObtainFile): self
    {
        $this->useArgToObtainFile = $useArgToObtainFile;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * Define isExecuted to false
     */
    public function run(string $file)
    {
        $this->isExecuted = false;
        
        parent::run($file);
    }
    
    /**
     * {@inheritdoc}
     * Use the property fileInArg value if useArgToObtainFile is false
     */
    public function obtainFileFromArg(): string
    {
        if ($this->useArgToObtainFile === true) {
            return parent::obtainFileFromArg();
        }
        
        return $this->fileInArg;
    }
    
    /**
     * {@inheritdoc}
     * Return true if the property useArgToObtainFile is false
     */
    protected function checkFile(): bool
    {
        if ($this->useArgToObtainFile === true) {
            return parent::checkFile();
        }
        
        return true;
    }

    /**
     * {@inheritdoc}
     * Not executed anything. Just pass isExecuted to true.
     */
    protected function execFile()
    {
        $this->isExecuted = true;
    }
}
