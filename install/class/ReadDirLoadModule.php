<?php

namespace BFW\Install;

/**
 * Class use to detect modules in a directory and sub-directories
 */
class ReadDirLoadModule extends ReadDirectory
{
    /**
     * {@inheritdoc}
     */
    protected function fileAction($fileName, $pathToFile)
    {
        //Call parent method to check ignored files
        $parentAction = parent::fileAction($fileName, $pathToFile);

        if ($parentAction !== null) {
            return $parentAction;
        }

        //Detect a module infos json file
        if ($fileName === 'bfwModulesInfos.json') {
            $this->list[] = $pathToFile;

            return 'break';
        }

        return '';
    }
}
