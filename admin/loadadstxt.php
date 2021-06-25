<?php
	session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$handle = fopen("ads.txt", "r");
	if ($handle) {
	    while (($line = fgets($handle)) !== false) {
		    $line = trim($line);
		    $time = time();
	        $sql = "INSERT INTO " . ADSTXT . " (LineTxt, Time, NoEdit) VALUES ('$line','$time',0)";
			//$db->query($sql);
	    }
	
	    fclose($handle);
	} else {
	    // error opening the file.
	} 