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
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	//exit(0);
	/*
	$dbuser2 = "root";
	$dbpass2 = "vidoopre-pass_2020";
	$dbhost2 = "aa1nh4ao2doeo1w.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy-advertisers-panel";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	*/
	$dbuser3 = "root";
	$dbpass3 = "vidooprod-pass_2020";
	$dbhost3 = "aa4mgb1tsk2y6v.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname3 = "vidoomy-advertisers-panel";
	$db3 = new SQL($dbhost3, $dbname3, $dbuser3, $dbpass3);
	
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