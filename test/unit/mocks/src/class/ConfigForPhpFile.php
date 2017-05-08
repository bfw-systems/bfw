<?php

namespace BFW\test\unit\mocks;

/**
 * Mock for Config class
 * Only to Config unit test
 * 
 * Because Atoum doesn't overload protected method.
 * 
 * @TODO : Use Override helpers into Config instead of this class.
 */
class ConfigForPhpFile extends \BFW\Config
{
    /**
     * {@inheritdoc}
     * Force some values for unit test
     */
    protected function loadPhpConfigFile($fileKey, $filePath)
    {
        $debugValue = false;
        if (strpos($filePath, '/class/core/Options.php') !== false) {
            $debugValue = true;
        }
        
        $this->config[$fileKey] = (object) [
            'debug' => $debugValue,
            'errorRenderFct' => (object) [
                'default' => '\BFW\Core\Errors::defaultErrorRender',
                'cli'     => '\BFW\Core\Errors::defaultCliErrorRender'
            ],
            'fixNullValue' => null
        ];
    }
}
