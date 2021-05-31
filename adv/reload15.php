<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/reports_/adv/config.php');
	//require('/var/www/html/login/reports_/adv/config_pre.php');
	require('/var/www/html/login/db.php');
	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	
	require('/var/www/html/login/reports_/adv/common.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');
	
	$sql = "SELECT * FROM reports_back_20210517 WHERE Date = '2021-05-15'";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Camp = $db->fetch_array($query)){
			$id = $Camp['id'];
			$SSP = $Camp['SSP'];
			$idCampaing = $Camp['idCampaing'];
			$idCountry = $Camp['idCountry'];
			$Requests = $Camp['Requests'];
			$Bids = $Camp['Bids'];
			$Impressions = $Camp['Impressions'];
			$Revenue = $Camp['Revenue'];
			$VImpressions = $Camp['VImpressions'];
			$Clicks = $Camp['Clicks'];
			$CompleteV = $Camp['CompleteV'];
			$Complete25 = $Camp['Complete25'];
			$Complete50 = $Camp['Complete50'];
			$Complete75 = $Camp['Complete75'];
			$CompleteVPer = $Camp['CompleteVPer'];
			$Rebate = $Camp['Rebate'];
			$Date = $Camp['Date'];
			$Hour = $Camp['Hour'];
			$DemangTagId = $Camp['DemangTagId'];
			
			$sql = "INSERT INTO reports
					(id, SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, CompleteVPer, Rebate, Date, Hour, DemangTagId) 
					VALUES ($id, $SSP, $idCampaing, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', '$Complete75', '$CompleteVPer', '$Rebate', '$Date', '$Hour', '$DemangTagId')";
			$db->query($sql);
			//exit(0);
			echo $id . "\n";
		}
	}