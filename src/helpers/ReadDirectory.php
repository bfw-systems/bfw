<?php

namespace BFW\Helpers;

/**
 * Class use to read a directory and sub-directories
 */
class ReadDirectory
{
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
    private $ignore = ['.', '..'];

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
            return;
        }

        //Tant qu'il y a des fichiers à lire dans le dossier
        while (($file = readdir($dir)) !== false) {
            $action = $this->fileAction($file, $path);

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
     * Action to do when a file is found.
     * 
     * @param string $fileName The file's name
     * @param string $pathToFile The file's path
     * 
     * @return string
     */
    protected function fileAction($fileName, $pathToFile)
    {
        if (in_array($fileName, $this->ignore)) {
            return 'continue';
        }
    }
    
    /**
     * Recall ReadDirectory to read this directory
     * This is to avoid having the recursion error
     * 
     * @param string $directory
     */
    protected function dirAction($directory)
    {
        $read = new $this->calledClass($this->list);
        $read->run($directory);
    }
}
