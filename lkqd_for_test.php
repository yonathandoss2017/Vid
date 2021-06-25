<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('config.php');
	require('constantes.php');
	require('db.php');
	require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$Date = date('Y-m-d', time() - 3600);
	
	//exit(0);
	$Date = '2020-03-23';
	$Date1 = '2020-03-24';
	
	$headers = array(
	    'Content-Type:application/json',
	    'Authorization: Basic '. base64_encode("wX2ZJqf1xkesZnSw8KsZIHyTjeyumwKc:UevGoz-SOIAd2xFm-uyXaZbOxXnI8ccs-4FR7KxhfNY") // <---
	);
	$post = array(
		"timeDimension" => "DAILY",
		"reportType" => array("PARTNER", "SITE"),
		"reportFormat" => "JSON",
		//"metrics" => array("IMPRESSIONS","REVENUE","CLICKS","FORMAT_LOADS","COST","PROFIT","OPPORTUNITIES"),
		"startDate" => $Date,
		"endDate" => $Date1,
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
	//print_r($decoded_result);
	
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
		$formatLoads = $entry->formatLoads;
		
		$timeAdded = time();
		$lastUpdate = time();
		
		$inserta = 0;
		/*
		$sql = "SELECT id FROM " . USERS . " WHERE LKQD_id = '$LKQDid' LIMIT 1";
		$idUser = $db->getOne($sql);
		if($idUser > 0){
			$inserta = 1;
		}
		*/
		
		$sql = "SELECT id FROM " . TAGS . " WHERE idTag = '$TagId' AND idPlatform = 1 ORDER BY id DESC LIMIT 1";
		$idTag = $db->getOne($sql);
		if($idTag > 0){
			$inserta = 1;	
			$sql = "SELECT idSite FROM " . TAGS . " WHERE id = '$idTag' LIMIT 1";
			$idSite = intval($db->getOne($sql));
			$sql = "SELECT idUser FROM " . TAGS . " WHERE id = '$idTag' LIMIT 1";
			$idUser = intval($db->getOne($sql));
		}else{
			$inserta = 0;
			/*
			$sql = "INSERT INTO " . TAGS . " (idUser, idTag, TagName) VALUES ($idUser, '$TagId', '$Tag')";
			$db->query($sql);
			$idTag = mysqli_insert_id($db->link);
			*/
		}
		
		
		
		if($inserta == 1){
			
			/*
			if($idSite == 11513 && date('d', time() - 3600) == 27){
				$Impressions = $formatLoads / 10.8;
				$Coste = $Impressions * 0.0015;
				$Revenue = $Coste * 1.4286;
			}
			*/
			/*
			if($idSite == 11630){
				$Impressions = $formatLoads / 4;
				$Coste = $Impressions * 0.0015;
				$Revenue = $Coste * 1.4286;
			}
			*/
			
			$sql = "SELECT id FROM stats_for_test WHERE idUser = '$idUser' AND idTag = '$idTag' AND Date = '$Date' AND Manual = 0";
			$idStat = $db->getOne($sql);
			if($idStat > 0){
				$sql = "UPDATE stats_for_test SET Impressions = '$Impressions', Opportunities = '$Opportunities', formatLoads = '$formatLoads', Revenue = '$Revenue', Coste = '$Coste', Clicks = '$Clicks', lastUpdate = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
				$db->query($sql);
			}else{
				$sql = "INSERT INTO stats_for_test (idUser, idTag, idSite, Impressions, Opportunities, formatLoads, Revenue, Coste, Clicks, timeAdded, lastUpdate, Date, Manual) VALUES ('$idUser', '$idTag', '$idSite', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$Clicks', '$timeAdded', '$lastUpdate', '$Date', 0)";
				$db->query($sql);
			}
			//echo $sql . "\n";
		}
		
	}
	echo 'OK 1';