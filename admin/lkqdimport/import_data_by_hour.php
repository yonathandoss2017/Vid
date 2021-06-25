<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../../config.php');
	require('../../constantes.php');
	require('../../db.php');
	require('../../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	//echo date("H:i:s\n", time());
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie.txt';

	//$DateS = date('Y-m-d', time() - 3600);
	//$DateS = '2019-05-04'  ;
	$DateX = '2019-05-XX';
	
	$headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'LKQD-Api-Version: 85',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36'
	);
	$post = array(
		"login" => "Vidoomy_admin",
		"password" => 'Vidoomy_LKQD2020'
	);
	
	$json_encode = json_encode($post);

	$url = 'https://ui-api.lkqd.com/sessions';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$json_encode);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	$result = curl_exec($ch);
	curl_close($ch);  
	
	//echo $result;
	
	$decoded_result = json_decode($result);
	//print_r($decoded_result);
	$sessionId = $decoded_result->data->sessionId;
	//exit(0);
	
	$headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'LKQD-Api-Version: 85',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/reports/7712',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36'
	);
	
	for($D = 25; $D <= 26; $D++){
		
		if($D < 10){
			$DateS = str_replace('XX',"0$D",$DateX);
		}else{
			$DateS = str_replace('XX',$D,$DateX);
		}
		
		echo $DateS;
	
		for($Hi = 0; $Hi <= 23; $Hi++){
	
			$post = array(
				"timeDimension" => "HOURLY",
				"reportType" => array("PARTNER", "SITE", "DOMAIN", "COUNTRY"),
				"metrics" => array("OPPORTUNITIES","IMPRESSIONS","CPM","REVENUE","COST","FORMAT_LOADS","CLICKS","FIRST_QUARTILES","MIDPOINTS","THIRD_QUARTILES","COMPLETED_VIEWS","AD_STARTS","VIEWABLE_IMPRESSIONS"),
				"reportFormat" => "JSON",
				"startDate" => $DateS,
				"startDateHour" => $Hi,
				"endDate" => $DateS,
				"endDateHour" => $Hi,
				"sort" => array(array(
					"field" => "FORMAT_LOADS",
					"order" => "desc"
					)),
				"timezone" => "America/New_York",
				"limit" => 6000,
				"offset" => 0,
				"whatRequest" => "breakdown"
			);
		
			$Ni = 0;
			$Nu = 0;
			$Nno = 0;
			$MoreResults = 1;
			while($MoreResults == 1){
				$json = json_encode($post);
				$url = 'https://ui-api.lkqd.com/reports';
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,$url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
				curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
			    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
				$result = curl_exec($ch);
				curl_close($ch);  
				$decoded_result = json_decode($result);
				//print_r($decoded_result);
				//exit(0);
				echo count($decoded_result->data->entries);
				echo "\n";
				echo date("H:i:s\n", time());
				
				$post["offset"] = $post["offset"] + 6000;
				echo "\n";
				echo $MoreResults = $decoded_result->data->hasMoreResults;
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
					
					$inserta = 0;
					
					$sql = "SELECT id FROM " . TAGS . " WHERE idTag = '$TagId' AND idPlatform = 1 ORDER BY id DESC LIMIT 1";
					$idTag = $db->getOne($sql);
					if($idTag > 0){
						$sql = "SELECT idSite, idUser FROM " . TAGS . " WHERE id = '$idTag' LIMIT 1";
						$query = $db->query($sql);
						if($db->num_rows($query) > 0){
							$TagData = $db->fetch_array($query);
							$idSite = $TagData['idSite'];
							$idUser = $TagData['idUser'];
							$inserta = 1;
						}else{
							$inserta = 0;
						}
					}else{
						$inserta = 0;
						/*
						$sql = "INSERT INTO " . TAGS . " (idUser, idTag, TagName) VALUES ($idUser, '$TagId', '$Tag')";
						$db->query($sql);
						$idTag = mysqli_insert_id($db->link);
						*/
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
					
						$sql = "SELECT id FROM reports WHERE 
						idUser = '$idUser' 
						AND idTag = '$idTag' 
						AND Domain = '$idDomain' 
						AND Country = '$idCountry' 
						AND Date = '$Date'
						AND Hour = '$Hour'
						";
						
						$idStat = $db->getOne($sql);
						if($idStat > 0){
							//echo $sql;
							/*
							$sql = "SELECT formatLoads FROM reports WHERE id = '$idStat' LIMIT 1";
							$oldFL = $db->getOne($sql);
							
							if($oldFL < $formatLoads){*/
								$sql = "UPDATE reports SET Impressions = '$Impressions', Opportunities = '$Opportunities', formatLoads = '$formatLoads', Revenue = '$Revenue', Coste = '$Coste', Extraprima =  (ExtraprimaP * $Revenue / 100), Clicks = '$Clicks', lastUpdate = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
								$db->query($sql);
							//}
							
							$Nu++;
						}else{
							$sql = "SELECT ExtraprimaP FROM reports_resume WHERE idUser = '$idUser' AND idTag = '$idTag' AND Domain = '$idDomain' AND Country = '$idCountry' AND Date = '$Date'";
							$ExtraprimaP = $db->getOne($sql);
							$Extraprima	= $ExtraprimaP * $Revenue / 100;
							
							$sql = "INSERT INTO reports (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date, Hour) VALUES ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Midpoints', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$Date', '$Hour')";
							$db->query($sql);
							$Ni++;
						}
					}else{
						$Nno++;
					}
					//usleep(20000);
				}
				//exit(0);
			}
		
		
		}// FOR HOURS
	}
	
	echo 'OK: ' . $Ni . ' - ' . $Nu . ' - ' . $Nno;