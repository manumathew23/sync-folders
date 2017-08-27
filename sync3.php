<?php

function sync() {

    $files = array();
    $folders = func_get_args();

    if (empty($folders)) {
        return FALSE;
    }

    // Get all files
    foreach($folders as $key => $folder) {
        // Normalise folder strings to remove trailing slash
        $folders[$key] = rtrim($folder, DIRECTORY_SEPARATOR);   
        $files += glob($folder . DIRECTORY_SEPARATOR . '*');    
    }


print_r($files);
    // Drop same files
    $uniqueFiles = array();
    foreach($files as $file) {
        $hash = md5_file($file);

        if ( ! in_array($hash, $uniqueFiles)) {
            $uniqueFiles[$file] = $hash; 
        }
    }


    // Copy all these unique files into every folder
    foreach($folders as $folder) {
        foreach($uniqueFiles as $file => $hash) {
                copy($file, $folder . DIRECTORY_SEPARATOR . basename($file));
        }
    }
    return TRUE;    
}

// usage

sync('/var/www/backup/a', '/var/www/a');
?>