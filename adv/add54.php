<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/reports_/adv/config.php');
	require('/var/www/html/login/db.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	//exit(0);
	$dbuser2 = "root";
	$dbpass2 = "vidoopre-pass_2020";
	$dbhost2 = "aa1nh4ao2doeo1w.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy-advertisers-panel";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$dbuser3 = "root";
	$dbpass3 = "vidooprod-pass_2020";
	$dbhost3 = "aa4mgb1tsk2y6v.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname3 = "vidoomy-advertisers-panel";
	$db3 = new SQL($dbhost3, $dbname3, $dbuser3, $dbpass3);
	
	require('/var/www/html/login/reports_/adv/common.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');
	
	$Date = date('Y-m-d', time() - (3600 * 4));
	//$Date = date('2020-03-28');
	//$Hour = date('H');
	$Hour = 23;
	
	/*
	$date2 = new DateTime($Date1);
	$date2->modify('-1 day');
	$Date2 = $date2->format('Y-m-d');
	*/
	
	$DemandTags = array();	
	$ActiveDeals = array();
	$CampaingData = array();
	$sql = "SELECT reports.* FROM reports
	INNER JOIN campaign ON campaign.id = reports.idCampaing
	WHERE campaign.advertiser_id = 39 AND campaign.type = 2 AND reports.Date BETWEEN '2020-03-25' AND '2020-03-31' AND reports.Impressions > 0";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Row = $db->fetch_array($query)){
			$idRow = $Row['id'];
			
			$Impressions = $Row['Impressions'];
			$CCompleteVPer = $Row['CompleteVPer'];
			
			$NewCompleteVPer = $CCompleteVPer * 0.054 + $CCompleteVPer;
			$CompleteV = round($Impressions * $NewCompleteVPer);
			
			$sql = "UPDATE reports SET CompleteVPer = '$NewCompleteVPer', CompleteV = '$CompleteV' WHERE id = $idRow LIMIT 1";
			echo $Impressions . ": " . $sql . "\n";
			$db->query($sql);
		}
	}