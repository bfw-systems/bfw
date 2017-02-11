<?php

namespace BFW\test\unit\mocks;

/**
 * Atoum doesn't overload protected method.
 */
class ConfigForPhpFile extends \BFW\Config
{
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
