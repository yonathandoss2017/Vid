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
	
	$Date = date('Y-m-d', time() - 1200);
	$Date = '2020-04-06';
	$DateToday = date('Y-m-d', time());
	
	if($Date != $DateToday){
		$LastU = 'Last Update';
	}else{
		$LastU = '';
	}
	
	$DateFrom = $Date;
	$DateTo = $Date;
	

	//$HFrom = date('G', time() - 1200);
	//$HTo = date('G', time() - 1200);
	$HFrom = 0;
	$HTo = 23;
	//sleep(rand(1,90));
	
	//echo "Import $DateFrom to $DateTo - $HFrom $HTo \n";
	//exit(0);
	$StartTime = round(microtime(true) * 1000);
	echo 'Start: ' . $StartTime . "\n";
	
	$STags = array(
		'dimension' => 'SITE',
		'operation' => 'include',
		'filters' => array (
			0 => array (
				'matchType' => 'id',
				'value' => '1113967',
				'label' => 'bolavipcom_dt',
	        ),
			1 => array (
	          	'matchType' => 'id',
	          	'value' => '1113968',
		        'label' => 'bolavipcom_mw',
			),
			2 => array (
				'matchType' => 'id',
				'value' => '1113969',
				'label' => 'redgolcl_dt',
			),
			3 => array (
				'matchType' => 'id',
				'value' => '1113970',
				'label' => 'redgolcl_mw',
			),
			4 => array (
				'matchType' => 'id',
				'value' => '1113971',
				'label' => 'lapaginamillonariacom_dt',
			),
			5 => array (
				'matchType' => 'id',
				'value' => '1113972',
				'label' => 'lapaginamillonariacom_mw',
			)
		)
	);
	
	$ImportData = getHourDataCSVSomeTags($STags, $DateFrom, $DateTo, $HFrom, $HTo);
	
	if($ImportData === false){
		echo "Loggin in... \n\n";
		logIn();
		$ImportData = getHourDataCSVSomeTags($Tags, $DateFrom, $DateTo, $HFrom, $HTo);
	}
	
	//print_r($ImportData);
	//exit(0);
	
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
		
		$sql = "DELETE FROM $TablaName WHERE Date = '$DateFrom' AND Hour BETWEEN '$HFrom' AND '$HTo' AND Manual = 0 AND idTag >= 13396 AND idTag <= 13401";
		$db->query($sql);
		
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
					
					$sql = "SELECT id FROM sites WHERE (siteurl LIKE '%$Domain%' OR  sitename LIKE '%$Domain%') AND deleted = 0 AND test = 1 ORDER BY id DESC LIMIT 1"; //
					$idSite = intval($db->getOne($sql));
					
					if($idSite == 0){
						$sql = "SELECT id FROM sites WHERE (siteurl LIKE '%$Domain%' OR  sitename LIKE '%$Domain%') AND deleted = 0 ORDER BY id DESC LIMIT 1"; //AND test = 1 
						$idSite = intval($db->getOne($sql));
						
						if($idSite == 0){
							$DomainNoDev = str_replace('dev','', $Domain);
							$sql = "SELECT id FROM sites WHERE (siteurl LIKE '%$DomainNoDev%' OR  sitename LIKE '%$DomainNoDev%') AND deleted = 0 LIMIT 1"; // AND test = 1 
							$idSite = intval($db->getOne($sql));
						}
					}
					
					if($idSite > 0){
						$sql = "SELECT PlatformType FROM " . TAGS . " WHERE idTag = '$TagId' LIMIT 1";
						$PlatformType = $db->getOne($sql);
						
						$sql = "SELECT idUser FROM sites WHERE id = $idSite LIMIT 1";
						$idUser = intval($db->getOne($sql));
						
						$sql = "SELECT id FROM " . TAGS . " WHERE idSite = '$idSite' AND PlatformType = $PlatformType AND idPlatform = 1 ORDER BY id DESC LIMIT 1";
						$idTag = intval($db->getOne($sql));
						
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
					/*
					if($idSite == 11630){
						$Impressions = $formatLoads / 4;
						$Coste = $Impressions * 0.0015;
						$Revenue = $Coste * 1.4286;
						$CompletedViews = $Impressions * 0.72;
					}
					*/
					
					
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
		
		echo "Hours Imported - LKQD\n";
		
		//exit(0);
		
		// CHECK IF
		if($Ni > 0){
			$Subject = 'Hourly Update OK ' . $LastU;
			$message = "Actualizacion realizada. $Ni registros insertados. Hour: $HFrom - $HTo Date: $Date";
			
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
			
			
			
			$mem_var = new Memcached('reps');
			$mem_var->addServer("localhost", 11211);
			$mem_var->flush(1);
			

			
			$Nins = 0;
			$Nis = 0;
			$Coma = "";
			$Values = "";
			
			$Countries = array();
			
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
				$idC = $Da['Country'];
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
				
				/*
				if(array_key_exists($idC, $Countries){
					$idCountry = $Countries[$idC];
				}else{
					$sql = "SELECT idVidoomy FROM reports_country_names WHERE id = '$idC' LIMIT 1";
					$idCountry = $db->getOne($sql);
					
					$Countries[$idC] = $idCountry;
				}
				*/
				
				$idCountry = $Da['Country'];
				
				
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
			$Subject = 'Hourly Update KO - 0 Registros ' . $LastU;
			$message = "$Ni registros insertados. Hour: $HFrom - $HTo Date: $Date - Bucle 1: $Bucle1 Bucle 2: $Bucle2";
		}
	}else{
		echo 'Error 0';
		$Subject = 'Hourly Update Error';
		$message = "Error 0";
	}
	