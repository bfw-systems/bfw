<?php

namespace BFW\Install;

class ReadDirectory
{
    /**
     * @var string $calledClass : Name of the current class.
     * For recall this correct class when she's extended.
     */
    protected $calledClass = '';
    
    /**
     * @var array $list : List all path found
     */
    protected $list;

    /**
     * @var $ignore : Item to ignored during the reading of directories
     */
    private $ignore = ['.', '..'];

    /*
     * Constructeur
     * 
     * @param array &$listFiles : List of file found
     */
    public function __construct(&$listFiles)
    {
        $this->calledClass = get_called_class();
        $this->list        = &$listFiles;
    }

    /**
     * Read all the directories
     *
     * @param string $path : Path to read
     */
    public function run($path)
    {
        $dir = opendir($path);
        if ($dir === false) {
            return;
        }

        //Tant qu'il y a des fichiers à lire dans le dossier
        while (($file = readdir($dir)) !== false) {
            $action = $this->fileAction($file, $dir);

            if ($action === 'continue') {
                continue;
            } elseif ($action === 'break') {
                break;
            }

            //If it's a directory and not a file
            if (is_dir($dir.'/'.$file)) {
                $this->dirAction($dir.'/'.$file);
                continue;
            }
        }

        closedir($dir);
    }

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
     * @param type $directory
     */
    protected function dirAction($directory)
    {
        $read = new $this->calledClass($this->list);
        $read->run($directory);
    }
}
