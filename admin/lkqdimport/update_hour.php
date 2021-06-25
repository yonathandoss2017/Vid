<?php	
	exit(0);
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$Stop = file_get_contents("/var/www/html/login/admin/lkqdimport/stop");
	if(intval($Stop) > 0){
		exit(0);
	}
	
	date_default_timezone_set('America/New_York');
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie_uh.txt';
	$Offset = 0;

	$DateS = date('Y-m-d', time() - 120);
	//$DateS = '2019-05-26';
	$Hi = intval(date('H', time() - 120));
	
	$sql = "TRUNCATE reports_last_hour";
	$db->query($sql);
	
	$Ni = 0;
	$Nu = 0;
	$Nno = 0;
	/*
	$MoreResults = 1;
	while($MoreResults == 1){
	*/
		$decoded_result = getResultsHour($DateS, $Hi);
		if(!is_object($decoded_result)){
			logIn();
			$decoded_result = getResultsHour($DateS, $Hi);
			if(!is_object($decoded_result)){
				stopUpdate();
			}
		}
		echo count($decoded_result->data->entries);
		echo "\n";
		echo date("H:i:s\n", time());
		
		//$Offset = $Offset + 6000;
		//echo "\n";
		//echo $MoreResults = $decoded_result->data->hasMoreResults;
		echo "\n";
		echo "$Ni - $Nu - $Nno";
		echo "\n";
		//exit(0);
	
		foreach($decoded_result->data->entries as $entry){
			$Impressions = $entry->adImpressions;
			$Opportunities = $entry->adOpportunities;
			$Revenue = $entry->revenue;
			$Coste = $entry->siteCost;
			$Clicks = $entry->adClicks;
			//$Wins = $entry->adWins;
			$Wins = 0;
			$LKQDid = $entry->fieldId;
			$LKQDuser = $entry->fieldName;
			$TagId = $entry->dimension2Id;
			$Tag = $entry->dimension2Name;
			$formatLoads = $entry->formatLoads;
			$Domain = $entry->dimension3Name;
			if(isset($entry->dimension4Name)){
				$Country = $entry->dimension4Name;
				$CountryCode = $entry->dimension4Id;
			}else{
				$Country = '';
				$CountryCode = '';
			}
			
			$adStarts = $entry->adStarts;
			$FirstQuartiles = $entry->adFirstQuartiles;
			$Midpoints = $entry->adMidpoints;
			$ThirdQuartiles = $entry->adThirdQuartiles;
			$CompletedViews = $entry->adCompletedViews;
			
			//$arTime = explode('T', $entry->timeDimension);
			//$Date = $arTime[0];
			$Date = $DateS;
			//$Hour = $arTime[1];
			$Hour = $Hi;
			
			$timeAdded = time();
			$lastUpdate = time();
			
			$inserta = 1;
			
			$sql = "SELECT id FROM " . TAGS . " WHERE idTag = '$TagId' AND idPlatform = 1 ORDER BY id DESC LIMIT 1";
			$idTag = $db->getOne($sql);
			if($idTag > 0){
				$sql = "SELECT idSite, idUser FROM " . TAGS . " WHERE id = '$idTag' LIMIT 1";
				$query = $db->query($sql);
				if($db->num_rows($query) > 0){
					$TagData = $db->fetch_array($query);
					$idSite = $TagData['idSite'];
					$idUser = $TagData['idUser'];
				}else{
					$idSite = 0;
					$idUser = 0;
				}
			}else{
				$idSite = 0;
				$idUser = 0;
			}
			
			if($inserta == 1){
				$Domain = mysqli_real_escape_string($db->link, $Domain);
				$sql = "SELECT id FROM reports_domain_names WHERE Name LIKE '$Domain' LIMIT 1";
				
				$idDomain = intval($db->getOne($sql));
				if($idDomain == 0){
					$sql = "INSERT INTO reports_domain_names (Name) VALUES ('$Domain')";
					
					$db->query($sql);
					$idDomain = mysqli_insert_id($db->link);
				}
				
				$Country = mysqli_real_escape_string($db->link, $Country);
				$sql = "SELECT id FROM reports_country_names WHERE Name LIKE '$Country' LIMIT 1";
				
				$idCountry = intval($db->getOne($sql));
				if($idCountry == 0){
					$sql = "INSERT INTO reports_country_names (Name, Code) VALUES ('$Country', '$CountryCode')";
					
					$db->query($sql);
					$idCountry = mysqli_insert_id($db->link);
				}
				

				$SumDCT = $idDomain + $idCountry + $idTag;
				$KeyP = intval(substr($SumDCT, -1));
				$ExtraprimaP = $ExtraP[$KeyP];
				$Extraprima	= $ExtraprimaP * $Revenue / 100;
				
				$sql = "INSERT INTO reports_last_hour (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date, Hour) VALUES ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Midpoints', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$Date', '$Hour')";
				$db->query($sql);
				$Ni++;

			}else{
				$Nno++;
			}
		}
	//}
	
	echo date('Y-m-d H:i:s');
	echo "\n";
	
	lockTable('reports');
	
	$sql = "DELETE FROM reports WHERE Date = '$DateS' AND Hour = '$Hi'";
	$db->query($sql);

	echo date('Y-m-d H:i:s');
	echo "\n";
	
	$sql = "SELECT * FROM reports_last_hour ORDER BY id ASC";
	$query = $db->query($sql);
	while($Row = $db->fetch_array($query)){
		$idUser = $Row['idUser'];
		$idTag = $Row['idTag'];
		$idSite = $Row['idSite'];
		$idDomain = $Row['Domain'];
		$idCountry = $Row['Country'];
		$Impressions = $Row['Impressions'];
		$Opportunities = $Row['Opportunities'];
		$formatLoads = $Row['formatLoads'];
		$Revenue = $Row['Revenue'];
		$Coste = $Row['Coste'];
		$ExtraprimaP = $Row['ExtraprimaP'];
		$Extraprima = $Row['Extraprima'];
		$Clicks = $Row['Clicks'];
		$Wins = $Row['Wins'];
		$adStarts = $Row['adStarts'];
		$FirstQuartiles = $Row['FirstQuartiles'];
		$Midpoints = $Row['MidViews'];
		$ThirdQuartiles = $Row['ThirdQuartiles'];
		$CompletedViews = $Row['CompletedViews'];
		$Date = $DateS;
		$Hour = $Hi;
		
		$sql = "INSERT INTO reports (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, Date, Hour) VALUES ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Midpoints', '$ThirdQuartiles', '$CompletedViews', '$Date', '$Hour')";
		$db->query($sql);
	}
	
	unlockTable('reports');
	
	echo date('Y-m-d H:i:s');
	echo "\n";