<?php

namespace BFW\Install;

use bultonFr\Utils\Files\ReadDirectory;

/**
 * Class use to detect modules in a directory and sub-directories
 */
class ReadDirLoadModule extends ReadDirectory
{
    /**
     * {@inheritdoc}
     */
    protected function itemAction(string $fileName, string $pathToFile): string
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
