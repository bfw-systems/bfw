<?php

namespace BFW\Core;

class Options extends \BFW\Options
{
    public function __construct($defaultOption, $options)
    {
        parent::__construct($defaultOption, $options);

        if ($this->options['rootDir'] === null) {
            $this->options['rootDir'] = $this->defineRootDir();
        }

        if ($this->options['vendorDir'] === null) {
            $this->options['vendorDir'] = $this->defineVendorDir();
        }

        $rootDirPosLastLetter   = strlen($this->options['rootDir']) - 1;
        $vendorDirPosLastLetter = strlen($this->options['vendorDir']) - 1;

        if ($this->options['rootDir'][$rootDirPosLastLetter] !== '/') {
            $this->options['rootDir'] .= '/';
        }

        if ($this->options['vendorDir'][$vendorDirPosLastLetter] !== '/') {
            $this->options['vendorDir'] .= '/';
        }
    }

    protected function defineVendorDir()
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

    protected function defineRootDir()
    {
        return dirname($this->defineVendorDir()).'/';
    }
}
