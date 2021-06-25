<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/reports_/adv/config.php');
	require('/var/www/html/login/db.php');
	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$sql = "SELECT * FROM reports WHERE (idCampaing = 235 OR idCampaing = 241) AND Date BETWEEN '2020-04-10' AND '2020-04-30'";
	$query = $db->query($sql);
	while($S = $db->fetch_array($query)){
		$idRow = $S['id'];
		
		$RandVI = rand(8400,8700)/10000;
		$VImpressions = round($S['Impressions'] * $RandVI);
		
		$sql = "UPDATE reports SET VImpressions = '$VImpressions' WHERE id = '$idRow' LIMIT 1";
		$db->query($sql);
		echo $sql . "\n";
	}