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
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	//exit(0);

	//$db2 = new SQL($advPre['host'], $advPre['db'], $advPre['user'], $advPre['pass']);
	$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	
	$Date = '2020-06-15';
	
	require('/var/www/html/login/reports_/adv/common.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');

	$sql = "SELECT * FROM campaign WHERE ssp_id = 4 AND status = 1 AND type = 2";
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		while($Camp = $db3->fetch_array($query)){
			$idCampaing = $Camp['id'];
			
			//print_r($Camp);
			//exit(0);
			
			
			$sql = "SELECT COUNT(*) FROM reports WHERE idCampaing = $idCampaing AND Date = '$Date'";
			if($db->getOne($sql) > 0){
				echo $Camp['name'] . "\n";
				$sql = "DELETE FROM reports WHERE idCampaing = $idCampaing AND Date = '$Date'";
				$db->query($sql);
			}
		}
	}
	
	
	
	//$sql = "SELECT id FROM reports WHERE SSP = 4 AND idCampaing = $idCampaing AND Date = '$Date' AND Hour = '$Hour' LIMIT 1";
	//$idStat = $db->getOne($sql);