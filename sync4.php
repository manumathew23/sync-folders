<?php

if (isset($_POST['submit'])) {
    $source_path = $_POST['source'];
    $destination_path = $_POST['destination'];
    $exclude_file_path = $_POST['exclude'];
    if (sync ($source_path, $destination_path, $exclude_file_path)) {
        echo "You have successfully Synchronized the folders "
            . $source_path . " and " . $destination_path;
    } else {
        echo "Sorry, Synchronization failed !!";
    }
}

function sync($source_path, $destination_path, $exclude_file_path)
{
    validatePath($source_path, $destination_path, $exclude_file_path);
    $exclude_files = listExcludedFiles($source_path, $exclude_file_path);
    $source_files = listFiles("$source_path/*");
    $source_files = str_replace($source_path, "", array_values($source_files));

    foreach ($source_files as $file) {
        $source = $source_path . $file;
        $destination = $destination_path . $file;
        if (in_array($source, $exclude_files)) {
            continue;
        }
        if (!file_exists($destination)) {
            if(is_dir($source)) {
                mkdir($destination);
            } else {
                copy($source, $destination);
            } 
        } else {
            if(!is_dir($source)) {
                if (!checkHash($source, $destination)) {
                    copy($source, $destination);
                }
            }
        }
    } 
    
    return true;
}

function validatePath($source, $destination, $exclude = NULL)
{
    $error = 0;
    if(!file_exists($source)) {
        echo "Please enter a valid source path";
        exit;
    } elseif (!file_exists($destination)) {
        echo "Please enter a valid destination path";
        exit;
    } elseif (!empty($exclude)) {
        if(!file_exists($exclude) || is_dir($exclude)) {
            echo "Please enter a valid path for exclude file";
            exit;
        }
    }

    return true;
}

function listFiles($pattern)
{
    $files = glob($pattern);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, listFiles($dir . '/' . basename($pattern)));
    }

    return $files;
}

function listExcludedFiles($source_path, $exclude)
{
    $exclude_patterns = [];
    $exclude_files = [];
    $fp = fopen($exclude, "r");
    while (($exclude_values = fgets($fp, 4096)) !== false) {
        array_push($exclude_patterns, trim($exclude_values));
    }
    fclose($fp);
    foreach ($exclude_patterns as $exclude_pattern) {
        $exclude = listFiles($source_path . "/" . $exclude_pattern); 
        foreach ($exclude as $exclude_value) {
            array_push($exclude_files, $exclude_value);
        }
    }

    return $exclude_files;
}

function checkHash($source_file, $destination_file)
{
    $source_hash = md5_file($source_file);
    $destination_hash = md5_file($destination_file);
    if($source_hash === $destination_hash) {

        return true;
    } else {

        return false;
    }
}