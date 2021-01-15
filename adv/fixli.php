<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/reports_/adv/config.php');
	//require('/var/www/html/login/reports_/adv/config_pre.php');
	require('/var/www/html/login/db.php');
	require('../../config.php');
	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	//exit(0);

	//$db2 = new SQL($advPre['host'], $advPre['db'], $advPre['user'], $advPre['pass']);
	$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	
	require('/var/www/html/login/reports_/adv/common.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');
	
	$CPM = 0.01;
	$CPV = 0.034000;
	
	$sql = "SELECT * FROM reports WHERE idCampaing = 294 AND Date >= '2020-05-14'";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Row = $db->fetch_array($query)){
			$idRow = $Row['id'];
			$newRev = $Row['Impressions'] * $CPM / 1000;;
			//$newRev = $Row['CompleteV'] * $CPV;
			
			$sql = "UPDATE reports SET Revenue = '$newRev' WHERE id = $idRow LIMIT 1";
			$db->query($sql);
			//echo "$sql \n";
		}
	}