<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	//require('countries.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	
	$sql = "SELECT * FROM " . SITES . " ORDER BY id ASC";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		//echo 'A';
		
		while($Site = $db->fetch_array($query)){
			$Filename = $Site['filename'];
			echo $siteId = $Site['id'];
			
			$Time = time();
			$Date = date('Y-m-d');
			
			//$arFilename = explode('/',$Filename);
			//$Filename = $arFilename[3];
			//echo 1;
			$NewCode = file_get_contents($Filename);
			$NewCode = mysqli_real_escape_string($db->link, $NewCode);
			
			$sql = "INSERT INTO " . ADS . " 
			(idSite, idSCode, idLKQD, divID, Type, Width, Height, Close, DFP, Override, HeightA, SPosition, CCode, Time, Date) 
			VALUES 
			('$siteId','0','','','5','0','0','0','0','0','0','0',\"$NewCode\",'$Time','$Date')";
			$db->query($sql);
			//exit(0);
		}
	}
	
?>