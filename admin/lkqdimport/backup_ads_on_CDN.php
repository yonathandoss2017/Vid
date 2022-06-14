<?php

//ini_set('memory_limit', '-1');

// Get real path for our folder
$rootPath = realpath('/var/www/html/ads/');

$ZipName = 'back' . date('YmdHis') . '.zip';
$NoZipName = 'back' . date('YmdHis');

// Initialize archive object
$zip = new ZipArchive();
$zip->open($ZipName, ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Create recursive directory iterator

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file)
{
    // Skip directories (they would be added automatically)
    if (!$file->isDir())
    {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
    }
}

// Zip archive will be created only after closing object
$zip->close();

$conn_id = ftp_connect("push-24.cdn77.com");

 // login with username and password
 $login_result = ftp_login($conn_id, 'user_xx81b7nd','5pSdLMysEAcPzp3M');
ftp_pasv($conn_id, true);

 // upload a file
 if (ftp_put($conn_id, "/www/backs_adsx3219/$NoZipName" , $ZipName, FTP_BINARY)) {
    echo "successfully uploaded\n";
    unlink($ZipName);
    exit;
 } else {
    echo "There was a problem while uploading\n";
    exit;
    }
 // close the connection
 ftp_close($conn_id);