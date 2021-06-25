<?php	
	//exit();
	
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
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	require('/var/www/html/login/admin/lkqdimport/common.php');
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie.txt';

	$Date = date('Y-m-d', time() - (24 * 3600));
	$DateToday = date('Y-m-d', time());
	
	if($Date != $DateToday){
		$LastU = 'Last Update';
	}else{
		$LastU = '';
	}
	
	$DateFrom = $Date;
	$DateTo = $Date;
	

	$HFrom = date('G', time() - 1200);
	$HTo = date('G', time() - 1200);
	$HFrom = 0;
	$HTo = 23;
	//sleep(rand(1,90));
	
	echo "Import $DateFrom to $DateTo - $HFrom $HTo \n";
	//exit(0);
	$StartTime = round(microtime(true) * 1000);
	echo 'Start: ' . $StartTime . "\n";
	
	
	$ImportData = getHourDataCSV($DateFrom, $DateTo, $HFrom, $HTo);
	
	if($ImportData === false){
		echo "Loggin in... \n\n";
		logIn();
		$ImportData = getHourDataCSV($DateFrom, $DateTo, $HFrom, $HTo);
	}
	
	echo 'Import Finish: ' . (round(microtime(true) * 1000) - $StartTime) . " COUNT: " . count($ImportData) . "\n";
	
	
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	checkTablesByDates($DateFrom, $DateTo);
	
	$TagsArray = array();
	$DomainsArray = array();
	$CountryArray = array();
	$CountryArrayVidoomy = array();
	$Bucle1 = "No";
	$Bucle2 = "No";
	
	if($ImportData !== false){//if(1 == 1){
		$N = 0;
		$Ni = 0;
		$Nno = 0;
		$Last = false;
		
		$Nins = 0;
		$Coma = "";
		$Values = "";
		
		$TablaName = getTableName($Date);
		$TablaNameResume = getTableNameResume($Date);
		$TablaNameResume2 = str_replace('_', '', $TablaNameResume);
		
		//$sql = "DELETE FROM $TablaName WHERE Date = '$DateFrom' AND Hour BETWEEN '$HFrom' AND '$HTo' AND Manual = 0";
		//$db->query($sql);
		
		foreach($ImportData as $DataK => $DataL){
			$Nn = 0;
			$Bucle1 = 'Si';

			foreach($DataL as $Line){
				$Bucle2 = 'Si';
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
				if($TagId == 1089819 || $TagId == 1089817){

					$sql = "SELECT id FROM sites WHERE (siteurl LIKE '%$Domain%' OR  sitename LIKE '%$Domain%') AND deleted = 0 LIMIT 1";
					//echo $sql . "\n";
					//exit(0);
					$idSite = intval($db->getOne($sql));
					
					if($idSite == 0){
						$DomainNoDev = str_replace('dev','', $Domain);
						$sql = "SELECT id FROM sites WHERE (siteurl LIKE '%$DomainNoDev%' OR  sitename LIKE '%$DomainNoDev%') AND deleted = 0 LIMIT 1";
						$idSite = intval($db->getOne($sql));
					}
					
					if($idSite > 0){
						$sql = "SELECT PlatformType FROM " . TAGS . " WHERE idTag = '$TagId' LIMIT 1";
						$PlatformType = $db->getOne($sql);
						
						$sql = "SELECT idUser FROM sites WHERE id = $idSite LIMIT 1";
						$idUser = intval($db->getOne($sql));
						
						$sql = "SELECT id FROM " . TAGS . " WHERE idSite = '$idSite' AND PlatformType = $PlatformType AND idPlatform = 1 ORDER BY id DESC LIMIT 1";
						$idTag = intval($db->getOne($sql));
						echo "PT: $PlatformType - idUser: $idUser - idTag: $idTag - idSite: $idSite";
						exit(0);
					}else{
						$idSite = 0;
						$idUser = 0;
						
						$sql = "SELECT id FROM " . TAGS . " WHERE idTag = '$TagId' AND idPlatform = 1 ORDER BY id DESC LIMIT 1";
						$idTag = intval($db->getOne($sql));
					}
					
				}elseif(array_key_exists($TagId, $TagsArray)){
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
					/*
					if($idSite == 11513 && date('d', time() - 1200) == 27){
						$Impressions = $formatLoads / 10.8;
						$Coste = $Impressions * 0.0015;
						$Revenue = $Coste * 1.4286;
						$CompletedViews = $Impressions * 0.72;
					}
					*/
					
					if($idSite == 11630){
						$Impressions = $formatLoads / 4;
						$Coste = $Impressions * 0.0015;
						$Revenue = $Coste * 1.4286;
						$CompletedViews = $Impressions * 0.72;
					}
					
					
					
					
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
						
						$sql = "SELECT idVidoomy FROM reports_country_names WHERE id = '$idCountry' LIMIT 1";
						$idCountryVidoomy = intval($db->getOne($sql));
						$CountryArrayVidoomy[$Country] = $idCountryVidoomy;
					}
	
					$SumDCT = $idDomain + $idCountry + $idTag;
					$KeyP = intval(substr($SumDCT, -1));
					$ExtraprimaP = $ExtraP[$KeyP];
					$Extraprima	= $ExtraprimaP * $Revenue / 100;
										
					
					$Nins++;
					$Ni++;
	
				}else{
					$Nno++;
				}
			}
			$N++;
		}
		
		echo "Hours Imported - LKQD\n";
		
		
		
	}else{
		echo 'Error 0';
		$Subject = 'Hourly Update Error';
		$message = "Error 0";
	}