<?php

namespace BFW\Core\Test\Mock;

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
    public function setExecutedFile($executedFile)
    {
        $this->executedFile = $executedFile;
        return $this;
    }
    
    /**
     * Getter to property fileInArg
     * 
     * @return string
     */
    public function getFileInArg()
    {
        return $this->fileInArg;
    }

    /**
     * Getter to property useArgToObtainFile
     * 
     * @return boolean
     */
    public function getUseArgToObtainFile()
    {
        return $this->useArgToObtainFile;
    }
    
    /**
     * Getter to property isExecuted
     * 
     * @return boolean
     */
    public function getIsExecuted()
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
    public function setFileInArg($fileInArg)
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
    public function setUseArgToObtainFile($useArgToObtainFile)
    {
        $this->useArgToObtainFile = $useArgToObtainFile;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     * Define isExecuted to false
     */
    public function run($file)
    {
        $this->isExecuted = false;
        
        parent::run($file);
    }
    
    /**
     * {@inheritdoc}
     * Use the property fileInArg value if useArgToObtainFile is false
     */
    public function obtainFileFromArg()
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
    protected function checkFile()
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
