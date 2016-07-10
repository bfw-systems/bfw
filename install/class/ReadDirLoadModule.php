<?php

namespace \BFW\Install;

class ReadDirLoadModule extends ReadDirectory 
{
    protected function fileAction($fileName, $pathToFile)
    {
        $parentAction = parent::fileAction($fileName, $pathToFile);
        
        if($parentAction !== '') {
            return $parentAction;
        }
        
        if(file_exists($pathToFile.'/bfwModulesInfos.json')) {
            $this->itemList[] = $pathToFile;
            
            return 'break';
        }
        
        return '';
    }
}
