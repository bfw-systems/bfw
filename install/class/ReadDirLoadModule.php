<?php

namespace BFW\Install;

class ReadDirLoadModule extends ReadDirectory
{
    /**
     * {@inheritdoc}
     */
    protected function fileAction($fileName, $pathToFile)
    {
        $parentAction = parent::fileAction($fileName, $pathToFile);

        if ($parentAction !== null) {
            return $parentAction;
        }

        if ($fileName === 'bfwModulesInfos.json') {
            $this->list[] = $pathToFile;

            return 'break';
        }

        return '';
    }
}
