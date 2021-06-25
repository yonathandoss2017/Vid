<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	//echo date("H:i:s\n", time());
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie.txt';

	//$DateS = date('Y-m-d', time() - 3600);
	$DateFrom = '2019-09-04';
	$DateTo = '2019-09-04';
	
	checkTablesByDates($DateFrom, $DateTo);

	$HFrom = 0;
	$HTo = 23;
	//$DateX = '2019-05-XX';
	$StartTime = round(microtime(true) * 1000);
	echo 'Start: ' . $StartTime . "\n";
	
	$ImportData = getHourDataCSV($DateFrom, $DateTo, $HFrom, $HTo);
	
	if($ImportData === false){
		echo "Loggin in... \n\n";
		logIn();
		$ImportData = getHourDataCSV($DateFrom, $DateTo, $HFrom, $HTo);
	}
	
	//print_r($ImportData[0]);
	//exit(0);
	
	//$sql = "SET bulk_insert_buffer_size =1024*1024*1024;";
	//$db->query($sql);
	
	echo 'Import Finish: ' . (round(microtime(true) * 1000) - $StartTime) . "\n";
	
	$TagsArray = array();
	$DomainsArray = array();
	$CountryArray = array();
	
	if($ImportData !== false){
		$N = 0;
		$Ni = 0;
		$Nno = 0;
		$Last = false;
		
		$Nins = 0;
		$Coma = "";
		$Values = "";
		foreach($ImportData as $DataK => $DataL){
			$Nn = 0;

			foreach($DataL as $Line){
				//echo $DataK . ": " . $Line . ' _ ' . $N . ' - ' . $Nn . "\n";
				if($N > 0){
					if($Nn == 0){
						if(strpos($Line, 'T') !== false){
							$arTime = explode("T", $Line);
							$Hour = $arTime[1];
							$Date = $arTime[0];
						}else{
							$Last = true;
							break;
						}
					}
					if($Nn == 1){ $LKQDuser = $Line; }
					if($Nn == 2){ $TagId = $Line; }
					if($Nn == 4){ $Domain = $Line; }
					if($Nn == 5){ $Country = $Line; }
					if($Nn == 6){ $Opportunities = takeComa($Line); }
					if($Nn == 7){ $Impressions = takeComa($Line); }
					if($Nn == 8){ $CPM = takeMoney($Line); }
					if($Nn == 9){ $Revenue = takeMoney($Line); }
					if($Nn == 10){ $Coste = takeMoney($Line); }
					if($Nn == 11){ $formatLoads = takeComa($Line); }
					if($Nn == 12){ $Clicks = takeComa($Line); }
					if($Nn == 13){ $FirstQuartiles = takeComa($Line); }
					if($Nn == 14){ $Midpoints = takeComa($Line); }
					if($Nn == 15){ $ThirdQuartiles = takeComa($Line); }
					if($Nn == 16){ $CompletedViews = takeComa($Line); }
					if($Nn == 17){ $adStarts = takeComa($Line); }
					$Wins = 0;
				}
				$Nn++;
			}
			
			
			if($N > 0 && $Last === false){
				$timeAdded = time();
				$lastUpdate = time();
				
				$inserta = 1;
				if(array_key_exists($TagId, $TagsArray)){
					$idTag = $TagsArray[$TagId]['idTag'];
					$idSite = $TagsArray[$TagId]['idSite'];
					$idUser = $TagsArray[$TagId]['idUser'];
				}else{
					$sql = "SELECT id FROM " . TAGS . " WHERE idTag = '$TagId' AND idPlatform = 1 ORDER BY id DESC LIMIT 1";
					$idTag = intval($db->getOne($sql));
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
					$TagsArray[$TagId]['idTag'] = $idTag;
					$TagsArray[$TagId]['idSite'] = $idSite;
					$TagsArray[$TagId]['idUser'] = $idUser;
				}
				
				
				if($inserta == 1){
					if(array_key_exists($Domain, $DomainsArray)){
						$idDomain = $DomainsArray[$Domain];
					}else{
						$DomainS = mysqli_real_escape_string($db->link, $Domain);
						$sql = "SELECT id FROM reports_domain_names WHERE Name LIKE '$DomainS' LIMIT 1";
						$idDomain = intval($db->getOne($sql));
						if($idDomain == 0){
							$sql = "INSERT INTO reports_domain_names (Name) VALUES ('$DomainS')";
							
							$db->query($sql);
							$idDomain = mysqli_insert_id($db->link);
						}
						$DomainsArray[$Domain] = $idDomain;
					}

					if(array_key_exists($Country, $CountryArray)){
						$idCountry = $CountryArray[$Country];
					}else{
						$CountryS = mysqli_real_escape_string($db->link, $Country);
						$sql = "SELECT id FROM reports_country_names WHERE Name LIKE '$CountryS' LIMIT 1";
						
						$idCountry = intval($db->getOne($sql));
						if($idCountry == 0){
							$sql = "INSERT INTO reports_country_names (Name, Code) VALUES ('$CountryS', '')";
							
							$db->query($sql);
							$idCountry = mysqli_insert_id($db->link);
						}
						$CountryArray[$Country] = $idCountry;
					}
	
					$SumDCT = $idDomain + $idCountry + $idTag;
					$KeyP = intval(substr($SumDCT, -1));
					$ExtraprimaP = $ExtraP[$KeyP];
					$Extraprima	= $ExtraprimaP * $Revenue / 100;
					
					$TablaName = getTableName($Date);
					
					
					
					//$sql = "INSERT INTO $TablaName (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date, Hour) VALUES ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Midpoints', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$Date', '$Hour'); ";
					
					$Values .= "$Coma ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Midpoints', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$Date', '$Hour')";
					$Coma = ",";
					if($Nins == 1000){
						//exit(0);
						$sql = "INSERT INTO $TablaName (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date, Hour) VALUES $Values ;";
						
						$db->query($sql);
						$Coma = "";
						$Nins = 0;
						$Values = "";
					}
					
					$Nins++;
					$Ni++;
	
				}else{
					$Nno++;
				}
			}
			$N++;
		}
		
		if($Nins > 1){
			$sql = "INSERT INTO $TablaName (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date, Hour) VALUES $Values ;";			
			$db->query($sql);
		}
		
	}else{
		echo 'Error 0';
	}
