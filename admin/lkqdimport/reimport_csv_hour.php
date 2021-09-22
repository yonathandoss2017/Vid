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
		'2020-02-14'
	);
	
	
	foreach($Dates as $Date){
		
		$DateFrom = $Date;
		$DateTo = $Date;
		
		$HFrom = 0;
		$HTo = 23;
		

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
		
		
		$DateSi = str_replace('-', '', $Date);
		$LogFileName = "complete_hours_$DateSi.csv";
	
		file_put_contents("/var/www/html/login/admin/lkqdimport/log/$LogFileName", serialize($ImportData));
		
				
		echo 'Import Finish: ' . (round(microtime(true) * 1000) - $StartTime) . " COUNT: " . count($ImportData) . "\n";
		
		$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
		$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);

		
		checkTablesByDates($DateFrom, $DateTo);
		$TablaName = getTableName($Date);
		$TablaNameResume = getTableNameResume($Date);
		$TablaNameResume2 = str_replace('_', '', $TablaNameResume);
		
		
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
			
			
			$sql = "DELETE FROM $TablaName WHERE Date = '$DateFrom' AND Hour BETWEEN '$HFrom' AND '$HTo'";
			$db->query($sql);
			
			//print_r($ImportData);
			
			foreach($ImportData as $DataK => $DataL){
				$Nn = 0;
				//print_r($DataL);
				foreach($DataL as $Line){
					//echo $DataK . ": " . $Line . ' _ ' . $N . ' - ' . $Nn . "\n";
					if($N > 0){
						if($Nn == 0){
							if(strpos($Line, '-') !== false){
								$arTime = explode("T", $Line);
								$Hour = $arTime[1];
								
								//$Hour = $HFrom;
								
								//$Date = $arTime[0];
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
						
						$D1 = new DateTime($Date);
						$D2 = new DateTime('2020-02-14');
						if($idSite == 11513 && $D1 >= $D2){
							$Impressions = $formatLoads / 10.8;
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
						}
		
						$SumDCT = $idDomain + $idCountry + $idTag;
						$KeyP = intval(substr($SumDCT, -1));
						$ExtraprimaP = $ExtraP[$KeyP];
						$Extraprima	= $ExtraprimaP * $Revenue / 100;
											
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
			
			echo "Hours Imported \n";

			
			$Subject = 'Hourly Update OK';
			$message = "Actualizacion realizada. $Ni registros insertados. Hour: $Hour Date: $Date";
			
			
			$Nins = 0;
			$Nis = 0;
			$Coma = "";
			$Values = "";
			
			$sql = "DELETE FROM $TablaNameResume WHERE Date = '$Date'";
			$db->query($sql);
			
			$sql = "SELECT 
				idUser, idTag, idSite, Domain, Country, Date, 
				SUM(Impressions) AS Impressions, 
			    SUM(Opportunities) AS Opportunities, 
			    SUM(formatLoads) AS formatLoads, 
			    SUM(Revenue) AS Revenue, 
			    SUM(Coste) AS Coste,
			    ExtraprimaP,
			    SUM(Extraprima) AS Extraprima,
			    SUM(Clicks) AS Clicks,
			    SUM(Wins) AS Wins,
			    SUM(adStarts) AS adStarts,
			    SUM(FirstQuartiles) AS FirstQuartiles,
			    SUM(MidViews) AS MidViews,
			    SUM(ThirdQuartiles) AS ThirdQuartiles,
			    SUM(CompletedViews) AS CompletedViews
		    
		    FROM $TablaName WHERE Date = '$Date' AND idUser > 0
		    GROUP BY idUser, idTag, idSite, Domain, Country";
			
			$query = $db->query($sql);
			while($Da = $db->fetch_array($query)){
				$Nins++;
				$Nis++;
				$idUser = $Da['idUser'];
				$idTag = $Da['idTag'];
				$idSite = $Da['idSite'];
				$idDomain = $Da['Domain'];
				$idCountry = $Da['Country'];
				$Impressions = $Da['Impressions'];
			    $Opportunities = $Da['Opportunities'];
			    $formatLoads = $Da['formatLoads'];
			    $Revenue = $Da['Revenue'];
			    $Coste = $Da['Coste'];
			    $ExtraprimaP = $Da['ExtraprimaP'];
			    $Extraprima = $Da['Extraprima'];
			    $Clicks = $Da['Clicks'];
			    $Wins = $Da['Wins'];
			    $adStarts = $Da['adStarts'];
			    $FirstQuartiles = $Da['FirstQuartiles'];
			    $MidViews = $Da['MidViews'];
			    $ThirdQuartiles = $Da['ThirdQuartiles'];
			    $CompletedViews = $Da['CompletedViews'];
			
				$Values .= "$Coma ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$Date')";
				$Coma = ", ";
				
				if($Nins > 5000){
					$sql = "INSERT INTO $TablaNameResume (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, Date) VALUES $Values ;";			
					$db->query($sql);
					$Nins = 0;
					$Values = "";
					$Coma = "";
				}
			}
			
			if($Nins > 1){
				$sql = "INSERT INTO $TablaNameResume (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, Date) VALUES $Values ;";			
				$db->query($sql);
			}
			
			$Nins = 0;
			$Nis = 0;
			$Coma = "";
			$Values = "";
			
			$sql = "DELETE FROM $TablaNameResume2 WHERE Date = '$Date'";
			$db2->query($sql);
			
			$sql = "SELECT * FROM $TablaNameResume WHERE Date = '$Date' AND idUser > 0 AND idSite > 0";
			
			$query = $db->query($sql);
			while($Da = $db->fetch_array($query)){
				$Nins++;
				$Nis++;
				$ID = $Da['id'];
				$idUser = $Da['idUser'];
				$idTag = $Da['idTag'];
				$idSite = $Da['idSite'];
				$idDomain = $Da['Domain'];
				$idCountry = $Da['Country'];
				$Impressions = $Da['Impressions'];
			    $Opportunities = $Da['Opportunities'];
			    $formatLoads = $Da['formatLoads'];
			    $Revenue = $Da['Revenue'];
			    $Coste = $Da['Coste'];
			    $ExtraprimaP = $Da['ExtraprimaP'];
			    $Extraprima = $Da['Extraprima'];
			    $Clicks = $Da['Clicks'];
			    $Wins = $Da['Wins'];
			    $adStarts = $Da['adStarts'];
			    $FirstQuartiles = $Da['FirstQuartiles'];
			    $MidViews = $Da['MidViews'];
			    $ThirdQuartiles = $Da['ThirdQuartiles'];
			    $CompletedViews = $Da['CompletedViews'];
			
				$Values .= "$Coma ('$ID', '$idUser', '$idTag', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$Revenue', '$Coste', '$ExtraprimaP', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Extraprima', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$Date', '$idSite', '$formatLoads')";
				$Coma = ", ";
				
				if($Nins > 3000){
					$sql = "INSERT INTO $TablaNameResume2 (id, iduser, id_tag, domain, country, impressions, opportunities, revenue, coste, extra_prima_p, clicks, wins, ad_starts, first_quartiles, extraprima, mid_views, third_quartiles, completed_views, time_added, last_update, date, idsite, formatloads) VALUES $Values ;";			
					$db2->query($sql);
					$Nins = 0;
					$Values = "";
					$Coma = "";
				}
			}
			
			if($Nins > 1){
				$sql = "INSERT INTO $TablaNameResume2 (id, iduser, id_tag, domain, country, impressions, opportunities, revenue, coste, extra_prima_p, clicks, wins, ad_starts, first_quartiles, extraprima, mid_views, third_quartiles, completed_views, time_added, last_update, date, idsite, formatloads) VALUES $Values ;";			
				$db2->query($sql);
			}
						
						
			
			$message .= "\nActualizada tabla de Resumen Server Nuevo: $Nis registros insertados.";
			
			
			
			echo "Resume Ready \n";
		}else{
			echo 'Error 0';
			$Subject = 'Hourly Update Error';
			$message = "Error 0";
		}
	
	}