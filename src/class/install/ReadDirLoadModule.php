<?php

namespace BFW\Install;

/**
 * Class use to detect modules in a directory and sub-directories
 */
class ReadDirLoadModule extends \BFW\Helpers\ReadDirectory
{
    /**
     * {@inheritdoc}
     */
    protected function itemAction($fileName, $pathToFile)
    {
        //Call parent method to check ignored files
        $parentAction = parent::itemAction($fileName, $pathToFile);

        if (!empty($parentAction)) {
            return $parentAction;
        }

        //Detect the file containing module infos
        if ($fileName === 'bfwModulesInfos.json') {
            $this->list[] = $pathToFile;

            return 'break';
        }

        return '';
    }
}
