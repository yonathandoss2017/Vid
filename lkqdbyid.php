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
	
	//$Date = date('Y-m-d');
	$Date1 = date('2019-01-22');
	$Date2 = date('2019-01-24');
	
	$headers = array(
	    'Content-Type:application/json',
	    'Authorization: Basic '. base64_encode("uzND3RtK6sjfcSd7FrkElQTimMlWt2mj:zACqDDqB1IjPAQfE8_HPu7XhrjijkXGvA69RNdybuAI") // <---
	);
	$post = array(
		"timeDimension" => "DAILY",
		"reportType" => array("PARTNER", "SITE"),
		"reportFormat" => "JSON",
		"startDate" => $Date1,
		"endDate" => $Date2,
		"timezone" => "America/New_York"
		//"limit" => 20
	);
	$json_encode = json_encode($post);
	

	$url = 'https://api.lkqd.com/reports';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$json_encode);
	$result = curl_exec($ch);
	curl_close($ch);  
	
	$decoded_result = json_decode($result);
	print_r($decoded_result);
	
	//exit(0);
	foreach($decoded_result->data->entries as $entry){
		$Impressions = $entry->adImpressions;
		$Opportunities = $entry->adOpportunities;
		$Revenue = $entry->revenue;
		$Coste = $entry->siteCost;
		$Clicks = $entry->adClicks;
		$LKQDid = $entry->fieldId;
		$LKQDuser = $entry->fieldName;
		$TagId = $entry->dimension2Id;
		$Tag = $entry->dimension2Name;
		$Date = $entry->timeDimension;
		$formatLoads = $entry->formatLoads;
		$timeAdded = time();
		$lastUpdate = time();
		
		if($LKQDid == '53616'){
			$inserta = 0;
			$sql = "SELECT id FROM " . USERS . " WHERE LKQD_id = '$LKQDid' LIMIT 1";
			$idUser = $db->getOne($sql);
			if($idUser > 0){
				$inserta = 1;
			}else{
				/*
				$sql = "INSERT INTO " . USERS . " (user, password, email, LKQD_User, LKQD_id, lastlogin, time, date) 
				VALUES ('$LKQDuser', '-', '-', '$LKQDuser', '$LKQDid', '0', '$timeAdded', '$Date')";
				$db->query($sql);
				$idUser = mysqli_insert_id($db->link);
				*/
			}
			
			$sql = "SELECT id FROM " . TAGS . " WHERE idTag = '$TagId' AND idPlatform = 1 ORDER BY id DESC LIMIT 1";
			$idTag = $db->getOne($sql);
			if($idTag > 0){
				$inserta = 1;
				$sql = "SELECT idSite FROM " . TAGS . " WHERE id = '$idTag' LIMIT 1";
				$idSite = intval($db->getOne($sql));
				//$sql = "SELECT idUser FROM " . TAGS . " WHERE id = '$idTag' LIMIT 1";
				//$idUser = intval($db->getOne($sql));
			}else{
				$inserta = 0;
				/*
				$sql = "INSERT INTO " . TAGS . " (idUser, idTag, TagName) VALUES ($idUser, '$TagId', '$Tag')";
				$db->query($sql);
				$idTag = mysqli_insert_id($db->link);
				*/
			}
			
			if($inserta == 1){
				$sql = "SELECT id FROM " . STATS . " WHERE idUser = '$idUser' AND idTag = '$idTag' AND Date = '$Date'";
				$idStat = $db->getOne($sql);
				if($idStat > 0){
					$sql = "UPDATE " . STATS . " SET Impressions = '$Impressions', Opportunities = '$Opportunities', formatLoads = '$formatLoads', Revenue = '$Revenue', Coste = '$Coste', Clicks = '$Clicks', lastUpdate = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
					$db->query($sql);
				}else{
					$sql = "INSERT INTO " . STATS . " (idUser, idTag, Impressions, Opportunities, formatLoads, Revenue, Coste, Clicks, timeAdded, lastUpdate, Date) VALUES ('$idUser', '$idTag', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$Clicks', '$timeAdded', '$lastUpdate', '$Date')";
					$db->query($sql);
				}
			}
		}
	}
	echo 'OK';