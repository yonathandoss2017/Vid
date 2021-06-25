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
	
		
	$Dates = array(
		'2019-04-01',
		'2019-04-02',
		'2019-04-03',
		'2019-04-04',
		'2019-04-05',
		'2019-04-06',
		'2019-04-07',
		'2019-04-08',
		'2019-04-09',
		'2019-04-10',
		'2019-04-11',
		'2019-04-12',
		'2019-04-13',
		'2019-04-14',
		'2019-04-15',
		'2019-04-16',
		'2019-04-17',
		'2019-04-18',
		'2019-04-19',
		'2019-04-20',
		'2019-04-21',
		'2019-04-22',
		'2019-04-23',
		'2019-04-24',
		'2019-04-25',
		'2019-04-26',
		'2019-04-27',
		'2019-04-28',
		'2019-04-29',
		'2019-04-30',
		'2019-05-01',
		'2019-05-02',
		'2019-05-03',
		'2019-05-04',
		'2019-05-05',
		'2019-05-06',
		'2019-05-07',
		'2019-05-08',
		'2019-05-09',
		'2019-05-10',
		'2019-05-11',
		'2019-05-12',
		'2019-05-13',
		'2019-05-14',
		'2019-05-15',
		'2019-05-16',
		'2019-05-17',
		'2019-05-18',
		'2019-05-19',
		'2019-05-20',
		'2019-05-21',
		'2019-05-22',
		'2019-05-23',
		'2019-05-24',
		'2019-05-25',
		'2019-05-26',
		'2019-05-27',
		'2019-05-28',
		'2019-05-29',
		'2019-05-30',
		'2019-05-31'
	);
	
	
	foreach($Dates as $Date){
		
		$DateFrom = $Date;
		$DateTo = $Date;
		
		
		//$DateFrom = '2018-01-02';
		//$DateTo = '2018-01-02';
		

		echo "Import $DateFrom to $DateTo \n";
		//exit(0);
		$StartTime = round(microtime(true) * 1000);
		echo 'Start: ' . $StartTime . "\n";
		
		$ImportData = getDayDataCSV($DateFrom, $DateTo);
		
		if($ImportData === false){
			echo "Loggin in... \n\n";
			logIn();
			$ImportData = getDayDataCSV($DateFrom, $DateTo);
		}
		
		//$Date = $DateFrom;
		$DateSi = str_replace('-','',$Date);
		//$LogFileName = "complete_month_$DateSi.csv";
		//file_put_contents("/var/www/html/login/admin/lkqdimport/log/$LogFileName", serialize($ImportData));
		
				
		echo 'Import Finish: ' . (round(microtime(true) * 1000) - $StartTime) . " COUNT: " . count($ImportData) . "\n";
		
		$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
		
		$TablaNameResume = getTableNameResume($Date);
		
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
			
			
			$sql = "DELETE FROM $TablaNameResume WHERE Date BETWEEN '$DateFrom' AND '$DateTo'";
			$db->query($sql);
			
			foreach($ImportData as $DataK => $DataL){
				$Nn = 0;
	
				foreach($DataL as $Line){
					if($N > 0){
						if($Nn == 0){
							if(strpos($Line, '-') !== false){
								$Date = trim($Line);
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
								
								$TagsArray[$TagId]['idTag'] = $idTag;
								$TagsArray[$TagId]['idSite'] = $idSite;
								$TagsArray[$TagId]['idUser'] = $idUser;
							}else{
								$idSite = 0;
								$idUser = 0;
								$idTag = 0;
								$inserta = 0;
								
								$TagsArray[$TagId]['idTag'] = $idTag;
								$TagsArray[$TagId]['idSite'] = $idSite;
								$TagsArray[$TagId]['idUser'] = $idUser;
							}
						}else{
							$idSite = 0;
							$idUser = 0;
							$idTag = 0;
							$inserta = 0;
							
							$TagsArray[$TagId]['idTag'] = $idTag;
							$TagsArray[$TagId]['idSite'] = $idSite;
							$TagsArray[$TagId]['idUser'] = $idUser;
						}
						
					}
					
					
					if($idTag != 0){
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
											
						$Values .= "$Coma ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Midpoints', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$Date')";
						$Coma = ",";
						if($Nins == 1000){
							//exit(0);
							$sql = "INSERT INTO $TablaNameResume (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date) VALUES $Values ;";
							
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
				$sql = "INSERT INTO $TablaNameResume (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date) VALUES $Values ;";			
				$db->query($sql);
			}
			
			echo "Month Imported \n";

			
		}else{
			echo 'Error 0';
		}
	
	}