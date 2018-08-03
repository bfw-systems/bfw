<?php

namespace BFW\Core;

use \Exception;

class Cli
{
    /**
     * @const ERR_NO_FILE_SPECIFIED_IN_ARG Exception code if the cli file to
     * run is not specified with the "-f" argument.
     */
    const ERR_NO_FILE_SPECIFIED_IN_ARG = 1201001;
    
    /**
     * @const ERR_CLI_FILE_NOT_FOUND Exception code if the cli file to run is
     * not found.
     */
    const ERR_FILE_NOT_FOUND = 1201002;
    
    /**
     * @var string The name of the executed cli file
     */
    protected $executedFile = '';
    
    /**
     * Getter to the property executedFile
     * 
     * @return string
     */
    public function getExecutedFile()
    {
        return $this->executedFile;
    }
    
    /**
     * Obtain the file to execute from the cli arg.
     * Search the arg "f" to get the value.
     * 
     * @return string The file path
     * 
     * @throws \Exception If no file is declared to be executed
     */
    public function obtainFileFromArg()
    {
        $cliArgs = getopt('f:');
        if (!isset($cliArgs['f'])) {
            throw new Exception(
                'Error: No file specified.',
                $this::ERR_NO_FILE_SPECIFIED_IN_ARG
            );
        }

        return CLI_DIR.$cliArgs['f'].'.php';
    }
    
    /**
     * Check the file to execute and run it
     * 
     * @param string $file
     * 
     * @return void
     */
    public function run($file)
    {
        $this->executedFile = $file;
        
        if ($this->checkFile() === true) {
            $this->execFile();
        }
    }
    
    /**
     * Check the file to execute.
     * 
     * @return boolean
     * 
     * @throws Exception
     */
    protected function checkFile()
    {
        if (!file_exists($this->executedFile)) {
            throw new Exception(
                'File to execute not found.',
                $this::ERR_FILE_NOT_FOUND
            );
        }
        
        return true;
    }
    
    /**
     * Execute the cli file into a different scope.
     * The new scope have access to $this of this class.
     * 
     * @return void
     */
    protected function execFile()
    {
        \BFW\Application::getInstance()
            ->getMonolog()
            ->getLogger()
            ->debug(
                'execute cli file.',
                ['file' => $this->executedFile]
            );
        
        $fctRunCliFile = function() {
            require($this->executedFile);
        };
        
        $fctRunCliFile();
    }
}
