#!/bin/bash
# BFW module manager script
#
# version : 3.0.0
# author : bulton-fr <bulton.fr@gmail.com>

# Function to display the help command
displayHelp()
{
    echo 'HELP Command :'
    echo "./vendor/bin/$commandName [options] [-- moduleName]"
    echo ''
    echo "$commandDesc"
    echo ''

    echo 'options list :'
    echo ' -h --help : Display this help'
    echo ' -b <path> --bfw-path <path> : Indicate the path to the BFW application. By default, the path is there is the vendor.'
    echo ' -V <path> --vendor-path <path> : Path to the vendor.'
    echo ' -a --all : To do the action for all modules find'

    if [ $hasReinstall == true ]; then
        echo ' -r --reinstall : If defined, the module will be deleted and added'
    fi

    echo ''
    echo 'module name : If declared, the action will be only for this module'

    exit 0
}

if [ $1 = '--help' ]; then
    displayHelp
fi

moduleName=''
bfwPath=''
vendorPath=''
reinstall=0
allModules=0

while [ -n "$1" ]; do # while loop starts
    case "$1" in
    -h | --help)
        displayHelp
        shift
        ;;
    -b | --bfw-path)
        bfwPath="$2"
        shift
        ;;
    -V | --vendor-path)
        vendorPath="$2"
        shift
        ;;
    -r | --reinstall)
        reinstall=1
        ;;
    -a | --all)
        allModules=1
        ;;
    --)
        shift
        break
        ;;
    esac
    shift
done

moduleName=$1

# Determine path to src/privateBinaries
# Thanks to https://stackoverflow.com/a/246128
currentDir=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
currentFile="$currentDir/$( basename $0 )"

if [ -L $currentFile ]; then
    currentFile="$currentDir/$( readlink -f "$currentFile")"
    currentDir=$( cd -P "$( dirname "${currentFile}" )" && pwd )
fi

# Create command to execute
scriptToCall="php $currentDir/moduleManagerExec.php"
scriptToCall="$scriptToCall --action=$action"

if [ -n $bfwPath ]; then
    scriptToCall="$scriptToCall --bfw-path=$bfwPath"
fi

if [ -n $vendorPath ]; then
    scriptToCall="$scriptToCall --vendor-path=$vendorPath"
fi

if [ -n $moduleName ]; then
    scriptToCall="$scriptToCall --module=$moduleName"
fi

if [ $reinstall -eq 1 ]; then
    scriptToCall="$scriptToCall --reinstall"
fi

if [ $allModules -eq 1 ]; then
    scriptToCall="$scriptToCall --all"
fi

$scriptToCall
