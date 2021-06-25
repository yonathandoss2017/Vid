<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('config.php');
	require('constantes.php');
	require('db.php');
	require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	require('libs/display.lib.php');
	
	$sql = "SELECT * FROM " . SITES . " WHERE deleted = 0";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Site = $db->fetch_array($query)){
			$idSite = $Site['id'];
			$sql = "SELECT COUNT(*) FROM " . ADS . " WHERE idSite = $idSite AND Type = 3";
			if($db->getOne($sql) > 0){
				$sql = "SELECT COUNT(*) FROM " . ADS . " WHERE idSite = $idSite AND Type = 10";
				if($db->getOne($sql) == 0){
					$CCode = '{3:1}{6:2}';
					$Time = time();
					$Date = date('Y-m-d')
					$sql = "INSERT INTO " . ADS . "(idSite, idSCode, idLKQD, divID, Type, Width, Height, Close, DFP, Override, HeightA, SPosition, CCode, Time, Date) 
					VALUES ('$idSite','0','AUTO DISPLAY','','10','0','0','0','0','0','0','', '$CCode' , '$Time','$Date')";
					$db->query($sql);
					newGenerateJS($siteId);
				}
			}
		}
	}	