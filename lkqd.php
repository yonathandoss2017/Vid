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
	//$Date = '2020-01-30';
	
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
		"endDate" => $Date,
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
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	$result = curl_exec($ch);
	curl_close($ch);  
	
	$decoded_result = json_decode($result);
	print_r($decoded_result);
	
	exit(0);
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
			
			$sql = "SELECT id FROM " . STATS . " WHERE idUser = '$idUser' AND idTag = '$idTag' AND Date = '$Date' AND Manual = 0";
			$idStat = $db->getOne($sql);
			if($idStat > 0){
				$sql = "UPDATE " . STATS . " SET Impressions = '$Impressions', Opportunities = '$Opportunities', formatLoads = '$formatLoads', Revenue = '$Revenue', Coste = '$Coste', Clicks = '$Clicks', lastUpdate = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
				$db->query($sql);
			}else{
				$sql = "INSERT INTO " . STATS . " (idUser, idTag, idSite, Impressions, Opportunities, formatLoads, Revenue, Coste, Clicks, timeAdded, lastUpdate, Date, Manual) VALUES ('$idUser', '$idTag', '$idSite', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$Clicks', '$timeAdded', '$lastUpdate', '$Date', 0)";
				$db->query($sql);
			}
			//echo $sql . "\n";
		}else{
			$sql = "SELECT id FROM stats_missing WHERE idTag = '$TagId' AND Date = '$Date'";
			$idStat = $db->getOne($sql);
			if($idStat > 0){			
				$sql = "UPDATE stats_missing SET Impressions = '$Impressions', formatLoads = '$formatLoads', Revenue = '$Revenue' WHERE id = '$idStat' LIMIT 1";
				$db->query($sql);
			}else{
				$sql = "INSERT INTO stats_missing (idTag, formatLoads, Impressions, Revenue, TagName, Date, Time) 
				VALUES ('$TagId', '$formatLoads', '$Impressions', '$Revenue', '$Tag', '$Date', '$timeAdded')";
				$db->query($sql);
			}
		}
		
	}
	echo 'OK 1';

	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$Day = $Date;
	
	$sql = "SELECT * FROM stats WHERE Date = '$Day'";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		
		$sql = "DELETE FROM stats WHERE date = '$Day'";
		$db2->query($sql);
		
		while($S = $db->fetch_array($query)){	
			
			$idS = $S['id'];
			$idUser = $S['idUser'];
			$idTag = $S['idTag'];
			$idSite = $S['idSite'];
			$Impressions = $S['Impressions'];
			$Opportunities = $S['Opportunities'];
			$formatLoads = $S['formatLoads'];
			$Revenue = $S['Revenue'];
			$Coste = $S['Coste'];
			
			$RevenueE = correctCurrency($S['Revenue'], 2);
			$CosteE = correctCurrency($S['Coste'], 2);
			
			$Clicks = $S['Clicks'];
			$timeAdded = $S['timeAdded'];
			$lastUpdate = $S['lastUpdate'];
			$Date = $S['Date'];
			
			$sql = "INSERT INTO stats (id, iduser, idtag, idsite, impressions, opportunities, format_loads, usd_revenue, eur_revenue, usd_cost, eur_cost, clicks, time_added, last_update, date)
				VALUES
				('$idS', '$idUser','$idTag','$idSite','$Impressions','$Opportunities','$formatLoads','$Revenue','$RevenueE','$Coste','$CosteE','$Clicks','$timeAdded','$lastUpdate','$Date')";
			$db2->query($sql);
			//break;
			
		}
	}
	
	echo ' OK 2';
	
	
	$dbuser3 = "root";
	$dbpass3 = "vidooDev-Pass_2020";
	$dbhost3 = "publisher-panel-for-dev.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname3 = "vidoomy";
	$db3 = new SQL($dbhost3, $dbname3, $dbuser3, $dbpass3);
	
	$Day = $Date;
	
	$sql = "SELECT * FROM stats WHERE Date = '$Day'";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		
		$sql = "DELETE FROM stats WHERE date = '$Day'";
		$db3->query($sql);
		
		while($S = $db->fetch_array($query)){	
			
			$idS = $S['id'];
			$idUser = $S['idUser'];
			$idTag = $S['idTag'];
			$idSite = $S['idSite'];
			$Impressions = $S['Impressions'];
			$Opportunities = $S['Opportunities'];
			$formatLoads = $S['formatLoads'];
			$Revenue = $S['Revenue'];
			$Coste = $S['Coste'];
			
			$RevenueE = correctCurrency($S['Revenue'], 2);
			$CosteE = correctCurrency($S['Coste'], 2);
			
			$Clicks = $S['Clicks'];
			$timeAdded = $S['timeAdded'];
			$lastUpdate = $S['lastUpdate'];
			$Date = $S['Date'];
			
			$sql = "INSERT INTO stats (id, iduser, idtag, idsite, impressions, opportunities, format_loads, usd_revenue, eur_revenue, usd_cost, eur_cost, clicks, time_added, last_update, date)
				VALUES
				('$idS', '$idUser','$idTag','$idSite','$Impressions','$Opportunities','$formatLoads','$Revenue','$RevenueE','$Coste','$CosteE','$Clicks','$timeAdded','$lastUpdate','$Date')";
			$db3->query($sql);
			//break;
			
		}
	}
	
	echo ' OK 3';