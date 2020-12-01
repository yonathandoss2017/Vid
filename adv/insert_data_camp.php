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
	//$dbhost3 = "aa4mgb1tsk2y6v.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbhost3 = "aa14extn6ty9ilx.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname3 = "vidoomy-advertisers-panel";
	$db3 = new SQL($dbhost3, $dbname3, $dbuser3, $dbpass3);
	
	require('/var/www/html/login/reports_/adv/common.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');
	
function calcPercents($Perc , $Impressions, $Complete){
	if($Perc == 25){
		$VarP = rand(2100, 2400) / 1000;
	}elseif($Perc == 50){
		$VarP = rand(1500, 1640) / 1000;
	}else{
		$VarP = rand(1150, 1260) / 1000;
	}
	
	$Diff = $Impressions - $Complete;
	$Result = $Impressions - round(($Diff / $VarP));
	
	if($Result < $Impressions){
		if($Result > $Complete){
			return $Result;
		}else{
			return $Complete;
		}
	}else{
		return $Impressions;
	}
}


INSERT INTO `reports` (`id`, `SSP`, `idCampaing`, `idCountry`, `Requests`, `Bids`, `Impressions`, `Revenue`, `VImpressions`, `Clicks`, `CompleteV`, `Complete25`, `Complete50`, `Complete75`, `CompleteVPer`, `Rebate`, `Date`, `Hour`) VALUES (NULL, '4', '547', '47', '71361', '0', '31231', '120.86', '27836', '437', '22280', '28732', '25609', '24360', '0.00', '0', '2020-07-31', '23');


	$sql = "SELECT * FROM campaign WHERE id = 547";// ORDER BY id DESC LIMIT 90
	$query = $db3->query($sql);
	$Camp = $db3->fetch_array($query);
	$idCamp = $Camp['id'];

	$Type = $Camp['type'];
	$CampaingData[$idCamp]['AgencyId'] = $Camp['agency_id'];
	
	$idCountry = 999;
	$sql = "SELECT COUNT(*) FROM campaign_country WHERE campaign_id = '$idCamp' ";
	if($db3->getOne($sql) == 1){
		$sql = "SELECT country_id FROM campaign_country WHERE campaign_id = '$idCamp' ";
		$idCountry = $db3->getOne($sql);
	}

	for($Hour=0; $Hour <= 23; $Hour++){
		$Date = '2020-07-31';

	
		$sql = "INSERT INTO reports
		(SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, CompleteVPer, Rebate, Date, Hour) 
		VALUES (4, $idCampaing, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', '$Complete75', '$CompleteVPerc', $Rebate, '$Date', '$Hour')";
		$db->query($sql);
		echo $sql . "\n";
	
	}