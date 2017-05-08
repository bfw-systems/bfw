<?php

namespace BFW\Core;

/**
 * Class to define options for BFW Core
 */
class Options extends \BFW\Options
{
    /**
     * Constructor
     * 
     * @param array $defaultOption : The default options from BFW Application
     * @param array $options : The options declared by user
     */
    public function __construct($defaultOption, $options)
    {
        parent::__construct($defaultOption, $options);

        //Search root directory if is not declared
        if ($this->options['rootDir'] === null) {
            $this->options['rootDir'] = $this->searchRootDir();
        }

        //Search the vendor directory if is not declared
        if ($this->options['vendorDir'] === null) {
            $this->options['vendorDir'] = $this->searchVendorDir();
        }

        //Get the length of paths to get the last character position
        $rootDirPosLastLetter   = strlen($this->options['rootDir']) - 1;
        $vendorDirPosLastLetter = strlen($this->options['vendorDir']) - 1;

        //If the last caracter is not a "/", add "/" at the end of the path
        
        if ($this->options['rootDir'][$rootDirPosLastLetter] !== '/') {
            $this->options['rootDir'] .= '/';
        }

        if ($this->options['vendorDir'][$vendorDirPosLastLetter] !== '/') {
            $this->options['vendorDir'] .= '/';
        }
    }

    /**
     * Find the vendor directory from the path of this file
     * (In theory we are into)
     * 
     * @return string
     */
    protected function searchVendorDir()
    {
        if (PHP_VERSION_ID >= 70000) {
            return dirname(__FILE__, 5).'/';
        }

        $rootDir = __FILE__;
        for ($i = 1; $i <= 5; $i++) {
            $rootDir = dirname($rootDir);
        }

        return $rootDir.'/';
    }

    /**
     * Find the root directory from the vendor directory
     * In theory of the vendor directory is on the root directory.
     * If not, the user can define path for vendor and root directory.
     * 
     * @return string
     */
    protected function searchRootDir()
    {
        return dirname($this->searchVendorDir()).'/';
    }
}
