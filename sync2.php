<?php

$sourcePath = "/var/www/sync/";
$destinationPath = "/var/www/backup/a/";
$options = array('folderPermission'=>0777,'filePermission'=>0777);

sync($sourcePath, $destinationPath, $options);

function sync($source, $dest, $options) 
{ 
    $result = true; 
    if (is_file($source)) { 
        copyFile($source, $dest, $options); 

    } elseif(is_dir($source)) { 
        copyDirectory($source, $dest, $options);

    } else { 
        $result = false; 
    } 
    return $result; 
} 

function copyFile($source, $dest, $options) 
{

    if ($dest[strlen($dest)-1] == '/') { 
        if (!file_exists($dest)) { 
            cmfcDirectory::makeAll($dest, $options['folderPermission'], true); 
        } 
        $destination = $dest."/" . basename($source); 
    } else { 
        $destination = $dest; 
    } 
    $result = copy($source, $destination);     
}

function copyDirectory($source, $dest, $options) 
{
    $excludedFiles = getExcludedFiles();
    createDirectory($source, $dest, $options);
    $dirHandle = opendir($source); 
    while ($file = readdir($dirHandle)) 
    { 
        $fileInfo = new SplFileInfo($file);
        $fileExtension = $fileInfo->getExtension();
        $fileName = $fileInfo->getBasename('.' . $fileExtension);
        array_push($excludedFiles, ".", "..");
        $fileDetails = array($file, $fileName, $fileExtension);
        if (count(array_intersect($excludedFiles, $fileDetails)) === 0) {
            if (!is_dir($source . "/" . $file)) { 
                $destination = $dest . "/" . $file; 
            } else { 
                $destination = $dest . "/" . $file; 
            } 
            echo "$source/$file -> $destination<br />"; 
            $result = sync($source."/".$file, $destination, $options); 
        }
    } 
    closedir($dirHandle);     
}

function createDirectory($source, $dest, $options)
{
    if ($dest[strlen($dest)-1]=='/') {
        $dest=$dest . basename($source); 
    } else {
        @mkdir($dest, $options['folderPermission']); 
        chmod($dest, $options['filePermission']); 
    }
}
function getExcludedFiles() 
{
    return array("manu", "bkh", "bkhp");
}

?>