<?php

namespace BFW\Helpers;

use \Exception;

/**
 * Class use to read a directory and sub-directories
 */
class ReadDirectory
{
    /**
     * @const ERR_RUN_OPENDIR Exception code if opendir fail
     */
    const ERR_RUN_OPENDIR = 1606001;
    
    /**
     * @var string $calledClass : Name of the current class.
     * For recall the correct class when she's extended.
     */
    protected $calledClass = '';
    
    /**
     * @var array $list : List all path found
     */
    protected $list;

    /**
     * @var array $ignore : Item to ignore during the reading of directories
     */
    protected $ignore = ['.', '..'];

    /*
     * Constructeur
     * 
     * @param array &$listFiles : List of file(s) found
     */
    public function __construct(&$listFiles)
    {
        $this->calledClass = get_called_class();
        $this->list        = &$listFiles;
    }
    
    /**
     * Getter accessor to the property calledClass
     * 
     * @return string
     */
    public function getCalledClass()
    {
        return $this->calledClass;
    }

    /**
     * Getter accessor to the property list
     * 
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Getter accessor to the property ignore
     * 
     * @return array
     */
    public function getIgnore()
    {
        return $this->ignore;
    }
    
    /**
     * Read all the directories
     *
     * @param string $path : Path to read
     * 
     * @return void
     */
    public function run($path)
    {
        $dir = opendir($path);
        if ($dir === false) {
            throw new Exception(
                'The directory can not be open. '
                .'See php error log for more informations.',
                self::ERR_RUN_OPENDIR
            );
        }

        //Tant qu'il y a des fichiers à lire dans le dossier
        while (($file = readdir($dir)) !== false) {
            $action = $this->itemAction($file, $path);

            if ($action === 'continue') {
                continue;
            } elseif ($action === 'break') {
                break;
            }
            
            //If it's a directory
            if (is_dir($path.'/'.$file)) {
                $this->dirAction($path.'/'.$file);
                continue;
            }
        }

        closedir($dir);
    }

    /**
     * Action to do when an item (file or directory) is found.
     * 
     * @param string $fileName The file's name
     * @param string $pathToFile The file's path
     * 
     * @return string
     */
    protected function itemAction($fileName, $pathToFile)
    {
        if (in_array($fileName, $this->ignore)) {
            return 'continue';
        }
        
        return '';
    }
    
    /**
     * Recall ReadDirectory to read this directory
     * This is to avoid having the recursion error
     * 
     * @param string $dirPath
     */
    protected function dirAction($dirPath)
    {
        $read = new $this->calledClass($this->list);
        $read->run($dirPath);
    }
}
