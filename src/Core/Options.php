<?php

namespace BFW\Core;

/**
 * Class to define options for BFW Core
 */
class Options extends \BFW\Options
{
    /**
     * Search the root and vendor paths if they are not declared.
     * 
     * @return $this
     */
    public function searchPaths(): self
    {
        //Search root directory if is not declared
        if ($this->options['rootDir'] === null) {
            $this->options['rootDir'] = $this->searchRootDir();
        }

        //Search the vendor directory if is not declared
        if ($this->options['vendorDir'] === null) {
            $this->options['vendorDir'] = $this->searchVendorDir();
        }
        
        return $this;
    }
    
    /**
     * Check root and vendor path declared or found.
     * 
     * @return $this
     */
    public function checkPaths(): self
    {
        if (empty($this->options['rootDir'])) {
            $this->options['rootDir'] .= '/';
        } else {
            $rootDirPosLastChar = strlen($this->options['rootDir']) - 1;
            
            if ($this->options['rootDir'][$rootDirPosLastChar] !== '/') {
                $this->options['rootDir'] .= '/';
            }
        }
        
        if (empty($this->options['vendorDir'])) {
            $this->options['vendorDir'] .= '/';
        } else {
            $vendorDirPosLastChar = strlen($this->options['vendorDir']) - 1;
            
            if ($this->options['vendorDir'][$vendorDirPosLastChar] !== '/') {
                $this->options['vendorDir'] .= '/';
            }
        }
        
        return $this;
    }

    /**
     * Find the vendor directory from the path of this file
     * (In theory we are into)
     * 
     * @return string
     */
    protected function searchVendorDir(): string
    {
        return dirname(__FILE__, 4).'/';
    }

    /**
     * Find the root directory from the vendor directory
     * In theory of the vendor directory is on the root directory.
     * If not, the user can define path for vendor and root directory.
     * 
     * @return string
     */
    protected function searchRootDir(): string
    {
        return dirname($this->searchVendorDir()).'/';
    }
}
