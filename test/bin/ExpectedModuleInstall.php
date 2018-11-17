<?php

namespace BFW\Test\Bin;

class ExpectedModuleInstall
{
    protected $moduleName;
    
    protected $configFiles = [];
    
    protected $scriptFiles = [];
    
    public function __construct(
        string $moduleName,
        array $configFiles = [],
        array $scriptFiles = []
    ) {
        $this->moduleName  = $moduleName;
        $this->configFiles = $configFiles;
        $this->scriptFiles = $scriptFiles;
    }
    
    public function getModuleName()
    {
        return $this->moduleName;
    }
    
    public function getConfigFiles()
    {
        return $this->configFiles;
    }
    
    public function getScriptFiles()
    {
        return $this->scriptFiles;
    }
        
    public function generateInstallOutput(bool $reinstall = false): string
    {
        $output = $this->moduleName." : Run install.\n"
            ." > Create symbolic link ... \033[1;32mDone\n\033[0m"
            ." > Copy config files :\n"
            ." >> Create config directory for this module ... \033[1;32mDone\n\033[0m"
            ." >> Copy manifest.json ... \033[1;32mDone\n\033[0m"
        ;
        
        foreach ($this->configFiles as $configFilename) {
            $output .= " >> Copy ".$configFilename." ... \033[1;32mDone\n\033[0m";
        }
            
        $output .= " > Check install specific script :\n";
        
        if ($this->scriptFiles !== []) {
            $output .= " >> \033[1;33mScripts find. Add to list to execute.\n\033[0m";
        } else {
            $output .= " >> \033[1;33mNo specific script declared. Pass\n\033[0m";
        }
        
        return $output;
    }
    
    public function generateScriptOutput(): string
    {
        $output = " > Read for module ".$this->moduleName."\n";
        
        if ($this->scriptFiles === []) {
            $output .= " >> No script to run.\n";
            return $output;
        }
        
        foreach ($this->scriptFiles as $scriptFilename => $scriptOutput) {
            $output .= " >> \033[1;33mExecute script ".$scriptFilename."\n\033[0m";
            $output .= $scriptOutput;
        }
        
        return $output."\n";
    }
}
