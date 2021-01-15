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
	
	$Date = '2020-06-14';
	$Yesterday = '2020-06-13';
	
	require('/var/www/html/login/reports_/adv/common.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');
	
	$sql = "SELECT * FROM campaign WHERE ssp_id = 4 AND status = 1 AND type = 2";
	$query1 = $db3->query($sql);
	if($db3->num_rows($query1) > 0){
		while($Camp = $db3->fetch_array($query1)){
			$idCampaing = $Camp['id'];
			
			$sql = "SELECT * FROM reports WHERE idCampaing = $idCampaing AND Date = '$Yesterday'";
			$query = $db->query($sql);
			if($db->num_rows($query) > 0){
				while($Sta = $db->fetch_array($query)){
					$idCampaing = $Sta['idCampaing'];
					$idCountry = $Sta['idCountry'];
					$Hour = $Sta['Hour'];
					
					$Requests = intval($Sta['Requests'] * 0.7);
					$Bids = intval($Sta['Bids'] * 0.7);
					$Impressions = intval($Sta['Impressions'] * 0.7);
					$VImpressions = intval($Sta['VImpressions'] * 0.7);
					$Clicks = intval($Sta['Clicks'] * 0.7);
					$VImpressions = intval($Sta['VImpressions'] * 0.7);
					$CompleteV = intval($Sta['CompleteV'] * 0.7);
					$Complete25 = intval($Sta['Complete25'] * 0.7);
					$Complete50 = intval($Sta['Complete50'] * 0.7);
					$Complete75 = intval($Sta['Complete75'] * 0.7);
					
					$CompleteVPerc = 0;
					
					$Revenue = $Sta['Revenue'] * 0.7;
					$Rebate = $Sta['Rebate'] * 0.7;
					
					$sql = "INSERT INTO reports
					(SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, CompleteVPer, Rebate, Date, Hour) 
					VALUES (4, $idCampaing, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', '$Complete75', '$CompleteVPerc', $Rebate, '$Date', '$Hour')";
					$db->query($sql);
					//echo $sql . "\n\n";
				}
				//exit(0);
			}
			
		}
	}