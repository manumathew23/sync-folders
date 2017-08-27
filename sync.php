<?php
//Path for file with exclude patterns
$excludeFile = "/var/www/exclude.txt";

//Source path
$source = "/var/www/sync/";

//Destination path
$destination = "root@192.168.6.207:/var/www/backup/";

exec('rsync -e ssh -rz --exclude-from ' . $excludeFile . ' ' . $source . ' ' . $destination);
?>