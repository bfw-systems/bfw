<?php

function testDirectoryOrFile($installDir, $dir)
{
    echo ' > '.$dir."\n";
    
    echo ' >> Exists ';
    if (!file_exists($installDir.'/'.$dir)) {
        echo "\033[1;31m[Fail]\033[0m\n";
        exit(1);
        
        return false;
    }
    echo "\033[1;32m[OK]\033[0m\n";
    
    echo ' >> Readable ';
    if (!is_readable($installDir.'/'.$dir)) {
        echo "\033[1;31m[Fail]\033[0m\n";
        exit(1);
        
        return false;
    }
    echo "\033[1;32m[OK]\033[0m\n";
    
    echo ' >> Writable ';
    if (!is_writable($installDir.'/'.$dir)) {
        echo "\033[1;31m[Fail]\033[0m\n";
        exit(1);
        
        return false;
    }
    echo "\033[1;32m[OK]\033[0m\n";
    
    return true;
}
