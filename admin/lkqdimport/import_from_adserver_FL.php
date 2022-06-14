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
	
	
	$Date = date('Y-m-d', time());
	
	$TagsArray = array();
	$DomainsArray = array();
	$CountryArray = array();
	$CountryArrayVidoomy = array();
	$ArrayRates = array();
	$Coma = "";
	$Values = "";
	$Nins = 0;
	$Ni = 0;
	$HoursArray = array();
	
function calcPercents($Perc , $Impressions, $Complete){
	if($Perc == 25){
		$VarP = rand(2100, 2400) / 1000;
	}elseif($Perc == 50){
		$VarP = rand(1500, 1640) / 1000;
	}else{
		$VarP = rand(1150, 1260) / 1000;
	}
	
	$Diff = $Impressions - $Complete;
	$Result = $Impressions - round(($Diff / $VarP));
	
	if($Result < $Impressions){
		if($Result > $Complete){
			return $Result;
		}else{
			return $Complete;
		}
	}else{
		return $Impressions;
	}
}

	$arraySpecialFill = array('Belgium', 'Netherlands', 'Turkey', 'United Kingdom', 'Trinidad and Tobago', 'Jamaica');
	$SpecialFill25 = array('Jamaica');
	$SpecialFill12 = array('Belgium', 'Trinidad and Tobago');
	$SpecialFill6 = array('Turkey');
	$arraySpecialFillWL = array('Italy', 'Portugal', 'Germany', 'Greece');
	
	
	foreach($arraySpecialFill AS $ISO => $Cntry){
		$CntryFile = str_replace(' ', '', $Cntry);
		//$FileCSV = '/var/www/html/login/admin/lkqdimport/cpm_blacklist'.$new.'/'.$CntryFile.'.csv';
		$FileCSV = '/var/www/html/login/admin/lkqdimport/cpm_blacklist/'.$CntryFile.'.csv';
		
		if(file_exists($FileCSV)){
			$Csv = array_map('str_getcsv', file($FileCSV));
			foreach($Csv as $Co){
				$BLDomains[$ISO][] = $Co[0];
			}
		}else{
			$BLDomains[$ISO][] = 'none';
		}
	}
	
	$WLDomains = array();	
	foreach($arraySpecialFillWL AS $Cntry){
		$CntryFile = str_replace(' ', '', $Cntry);
		$FileCSV = '/var/www/html/login/admin/lkqdimport/cpm_blacklist/'.$CntryFile.'WL.csv';
		
		if(file_exists($FileCSV)){
			$Csv = array_map('str_getcsv', file($FileCSV));
			foreach($Csv as $Co){
				$WLDomains[$Cntry][] = $Co[0];
			}
		}else{
			$WLDomains[$Cntry][] = 'none';
		}
	}
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	$DateDruid1 = new DateTime();
	$DateDruid1->setTimezone(new DateTimeZone('UTC'));
	$DateDruid1->modify("-102 hour");
	
	$DateDruid2 = new DateTime();
	$DateDruid2->setTimezone(new DateTimeZone('UTC'));
	$DateDruid2->modify("-82 hour");
	
	$DateD1 = $DateDruid1->format('Y-m-d H:00:00');
	$DateD2 = $DateDruid2->format('Y-m-d H:59:59');
	$DateEST1 = $DateDruid1;
	$DateEST2 = $DateDruid2;
	
	$ch = curl_init( $druidUrl );
	
	$Query = "SELECT __time, Country, Domain, Zone, Publisher, SUM(sum_FormatLoads) AS FormatLoads, SUM(sum_Impressions) AS Impressions, SUM(sum_ClickThrus) AS Clicks, SUM(sum_FirstQuartiles) AS FirstQuartiles, SUM(sum_MidPoints) AS MidPoints, SUM(sum_ThirdQuartiles) AS ThirdQuartiles, SUM(sum_VideoCompletes) AS VideoCompletes FROM production_enriched_event_supply WHERE __time >= '$DateD1' AND  __time <= '$DateD2' GROUP BY __time, Country, Domain, Zone, Publisher ORDER BY 5 DESC";
	//echo $Query . "\n\n";
	
	$context = new \stdClass();
	$context->sqlOuterLimit = 500000;//;
	
	$payload = new \stdClass();
	$payload->query = $Query;
	$payload->resultFormat = 'array';
	$payload->header = true;
	$payload->context = $context;
	
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($payload) );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec($ch);
	curl_close($ch);
	$result = json_decode($result) ;
	
	//print_r($result);
	//exit(0);
	
	$TablaName = getTableName($Date);
	$TablaNameResume = getTableNameResume($Date);
	$TablaNameResume2 = str_replace('_', '', $TablaNameResume);
	
	$DateEST1->setTimezone(new DateTimeZone('America/New_York'));
	$DateEST2->setTimezone(new DateTimeZone('America/New_York'));
	$HourEST1 = intval($DateEST1->format('H'));
	$HourEST2 = intval($DateEST2->format('H'));
//	$HourEST1 = intval(0);
//	$HourEST2 = intval(20);
	$DateESTs1 = $DateEST1->format('Y-m-d');
	$DateESTs2 = $DateEST2->format('Y-m-d');
	$sql = "DELETE FROM $TablaName WHERE Date = '$DateESTs1' AND Hour <= 20 AND Manual = 0 AND Player = 2";
//	$db->query($sql);
	echo $sql;
//	exit(0);
	
	foreach($result as $kres => $res){
		if($kres >= 1){
	
			$Time = $res[0];
			$Country = strtoupper($res[1]);
			$Domain = $res[2];
			$Zone = $res[3];
			$Publisher = $res[4];
			$OriginalformatLoads = $res[5];
			$OriginalImpressions = $res[6];
			$OriginalClicks = $res[7];
			
			$OriginalFirstQuartiles = $res[8];
			$OriginalMidpoints = $res[9];
			$OriginalThirdQuartiles = $res[10];
			$OriginalCompletedViews = $res[11];
			
			$adStarts = 0;
			$Wins = 0;
			$Opportunities = 0;
			
			$DateH = new DateTime($Time, new DateTimeZone('UTC'));
			$DateH->setTimezone(new DateTimeZone('America/New_York'));
			$Hour = intval($DateH->format('H'));
			$Date = $DateH->format('Y-m-d');
			$Day = intval($DateH->format('d'));
			$Month = intval($DateH->format('m'));
						
			if(array_key_exists($Zone.'-'.$Publisher, $TagsArray)){
				$idTag = $TagsArray[$Zone.'-'.$Publisher]['idTag'];
				$idSite = $TagsArray[$Zone.'-'.$Publisher]['idSite'];
				$idUser = $TagsArray[$Zone.'-'.$Publisher]['idUser'];
			}else{
				$sql = "SELECT id FROM " . USERS . " WHERE user = '$Publisher' LIMIT 1";
				$idUser = intval($db->getOne($sql));				
				
				$TimeLog = date('Y-m-d H:i:s');
				
				if($idUser > 0){
					$sql = "SELECT id FROM " . TAGS . " WHERE TagName = '$Zone' AND idPlatform = 1 AND idUser = '$idUser' ORDER BY id DESC LIMIT 1";
										
					$idTag = intval($db->getOne($sql));
					if($idTag > 0){
						$sql = "SELECT idSite FROM " . TAGS . " WHERE id = '$idTag' LIMIT 1";
						$query = $db->query($sql);
						if($db->num_rows($query) > 0){
							$TagData = $db->fetch_array($query);
							$idSite = $TagData['idSite'];
						}else{
							$idSite = 0;
							file_put_contents('/var/www/html/login/admin/lkqdimport/adserver_log.txt', "$TimeLog Site NOT FOUND, tag: $idTag \n", FILE_APPEND);
						}
					}else{
						file_put_contents('/var/www/html/login/admin/lkqdimport/adserver_log.txt', "$TimeLog Zone: $Zone idUser: $idUser NOT FOUND \n", FILE_APPEND);
						$idTag = 0;
						$idSite = 0;
					}
				}else{
					$idUser = 0;
					$idSite = 0;
					$idTag = 0;
					
					file_put_contents('/var/www/html/login/admin/lkqdimport/adserver_log.txt', "$TimeLog Publisher: $Publisher NOT FOUND \n", FILE_APPEND);
				}
				
				$TagsArray[$Zone.'-'.$Publisher]['idTag'] = $idTag;
				$TagsArray[$Zone.'-'.$Publisher]['idSite'] = $idSite;
				$TagsArray[$Zone.'-'.$Publisher]['idUser'] = $idUser;
			}
			
			if( array_key_exists($Date, $ArrayRates) ){
				$Rate = $ArrayRates[$Date];
			}else{
				$sql = "SELECT Rate FROM rates WHERE Date = '$Date'";
				$Rate = $db->getOne($sql);
				if($Rate <= 0){
					$DateForRate = new DateTime($Date);
				    $DateForRate->modify('-1 day');
				    
				    $JsonRates = file_get_contents('https://api.exchangeratesapi.io/' . $DateForRate->format('Y-m-d') . '?access_key=7df9c3b2eb8318c2294112c50f5209c8');
				    $Rates = json_decode($JsonRates);
				    $Rate = $Rates->rates->USD;
				    
				    $sql = "INSERT INTO rates (Date, Rate) VALUES ('$Date', '$Rate')";
				    $db->query($sql);
				}
				$ArrayRates[$Date] = $Rate;
			}

			$Revenue = $OriginalImpressions * 1.5 / 1000;
			if($Domain == 'huffingtonpost.es'){
				$Coste = $Revenue * 1;
			}else{
				$Coste = $Revenue * 0.4;
			}
			
			$timeAdded = time();
			$lastUpdate = time();
			
			$Impressions = 0;
			$CompletedViews = 0;
			$Clicks = 0;
		
			$FirstQuartiles = 0;
			$Midpoints = 0;
			$ThirdQuartiles = 0;
			
			$formatLoads = ceil($OriginalformatLoads * 94.5 / 100);
							
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

					$sql = "SELECT * FROM report_domain WHERE id = $idDomain";
					$reportDomainId = $db2->getOne($sql);

					if ($reportDomainId) {
						$sql = "UPDATE report_domain SET name = '{$DomainS}' WHERE id = $idDomain";
					} else {
						$sql = "INSERT INTO report_domain (id, name, is_alexa_rank_scanned, page_per_visit) VALUES ($idDomain, '{$DomainS}', 0, 0)";
					}

					$db2->query($sql);
				}
				$DomainsArray[$Domain] = $idDomain;
			}

			if(array_key_exists($Country, $CountryArray)){
				$idCountry = $CountryArray[$Country];
			}else{
				$CountryS = mysqli_real_escape_string($db->link, $Country);
				$sql = "SELECT id FROM reports_country_names WHERE Code LIKE '$CountryS' LIMIT 1";
				$idCountry = intval($db->getOne($sql));
				
				if($idCountry == 0){
					$sql = "INSERT INTO reports_country_names (Name, Code) VALUES ('', '$CountryS')";
					
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
			
			if($Revenue > 0){
				$RevenueEur = $Revenue / $Rate;
			}else{
				$RevenueEur = 0;
			}
			if($Coste > 0){
				$CosteEur = $Coste / $Rate;
			}else{
				$CosteEur = 0;
			}
			$RevenueEur = 0;
			$CosteEur = 0;
			$Coste = 0;
			$Revenue = 0;
			
			$HoursArray[$Hour] = $Hour;
								
			$Values .= "$Coma ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '2', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$RevenueEur', '$Coste', '$CosteEur', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Midpoints', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$Date', '$Hour')";
			$Coma = ",";
			if($Nins >= 1000){
				//exit(0);
				$sql = "INSERT INTO $TablaName (idUser, idTag, idSite, Domain, Country, Player, Impressions, Opportunities, formatLoads, Revenue, RevenueEur, Coste, CosteEur, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date, Hour) VALUES $Values ;";
				
				$db->query($sql);
				
				//echo $sql;
				//exit(0);
				
				$Coma = "";
				$Nins = 0;
				$Values = "";
			}
			
			$Nins++;
			$Ni++;
			
			
		}
	}
	
	if($Nins > 1){
		$sql = "INSERT INTO $TablaName (idUser, idTag, idSite, Domain, Country, Player, Impressions, Opportunities, formatLoads, Revenue, RevenueEur, Coste, CosteEur, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date, Hour) VALUES $Values ;";			
		$db->query($sql);
	}
	
	
	print_r($HoursArray);
	echo "Hours Imported - LKQD\n";
//	exit(0);
	$Nins = 0;
	$Nis = 0;
	$Coma = "";
	$Values = "";
	
	$sql = "DELETE FROM $TablaNameResume WHERE Date = '$Date' AND Player = 2";
	$db->query($sql);
	//exit(0);
	
	$sql = "SELECT 
		idUser, idTag, idSite, Domain, Country, Player, Date, 
		SUM(Impressions) AS Impressions, 
	    SUM(Opportunities) AS Opportunities, 
	    SUM(formatLoads) AS formatLoads, 
	    SUM(Revenue) AS Revenue,
	    SUM(RevenueEur) AS RevenueEur,
	    SUM(Coste) AS Coste,
	    SUM(CosteEur) AS CosteEur,
	    ExtraprimaP,
	    SUM(Extraprima) AS Extraprima,
	    SUM(Clicks) AS Clicks,
	    SUM(Wins) AS Wins,
	    SUM(adStarts) AS adStarts,
	    SUM(FirstQuartiles) AS FirstQuartiles,
	    SUM(MidViews) AS MidViews,
	    SUM(ThirdQuartiles) AS ThirdQuartiles,
	    SUM(CompletedViews) AS CompletedViews
    
    FROM $TablaName WHERE Date = '$Date' AND idUser > 0 AND Player = 2
    GROUP BY idUser, idTag, idSite, Domain, Country, Player";
	
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
	    $RevenueEur = $Da['RevenueEur'];
	    $Coste = $Da['Coste'];
	    $CosteEur = $Da['CosteEur'];
	    $ExtraprimaP = $Da['ExtraprimaP'];
	    $Extraprima = $Da['Extraprima'];
	    $Clicks = $Da['Clicks'];
	    $Wins = $Da['Wins'];
	    $adStarts = $Da['adStarts'];
	    $FirstQuartiles = $Da['FirstQuartiles'];
	    $MidViews = $Da['MidViews'];
	    $ThirdQuartiles = $Da['ThirdQuartiles'];
	    $CompletedViews = $Da['CompletedViews'];
	
		$Values .= "$Coma ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '2', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$RevenueEur', '$Coste', '$CosteEur', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$Date')";
		$Coma = ", ";
		
		if($Nins > 5000){
			$sql = "INSERT INTO $TablaNameResume (idUser, idTag, idSite, Domain, Country, Player, Impressions, Opportunities, formatLoads, Revenue, RevenueEur, Coste, CosteEur, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, Date) VALUES $Values ;";			
			$db->query($sql);
			$Nins = 0;
			$Values = "";
			$Coma = "";
		}
	}
	
	if($Nins > 1){
		$sql = "INSERT INTO $TablaNameResume (idUser, idTag, idSite, Domain, Country, Player, Impressions, Opportunities, formatLoads, Revenue, RevenueEur, Coste, CosteEur, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, Date) VALUES $Values ;";			
		$db->query($sql);
	}
	
	echo "Resumen 1 DONE\n";
	
	
	
	$Nins = 0;
	$Nis = 0;
	$Coma = "";
	$Values = "";
	
	$Countries = array();
	
	$sql = "DELETE FROM $TablaNameResume2 WHERE date = '$Date' AND player = 2";
	$db2->query($sql);
	//$db3->query($sql);
	
	$sql = "SELECT * FROM $TablaNameResume WHERE Date = '$Date' AND idUser > 0 AND idSite > 0 AND Player = 2";
	
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
		
		
		$Values .= "$Coma ('$ID', '$idUser', '$idTag', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$Revenue', '$Coste', '$ExtraprimaP', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Extraprima', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$Date', '$idSite', '$formatLoads', '0', '2')";
		$Coma = ", ";
		
		if($Nins > 2000){
			$sql = "INSERT INTO $TablaNameResume2 (id, iduser, id_tag, domain, country, impressions, opportunities, revenue, coste, extra_prima_p, clicks, wins, ad_starts, first_quartiles, extraprima, mid_views, third_quartiles, completed_views, time_added, last_update, date, idsite, formatloads, product, player) VALUES $Values ;";			
			$db2->query($sql);
			
			//echo $sql;
			//exit(0);
			
			//$db3->query($sql);
			$Nins = 0;
			$Values = "";
			$Coma = "";
		}
	}
	
	if($Nins > 1){
		$sql = "INSERT INTO $TablaNameResume2 (id, iduser, id_tag, domain, country, impressions, opportunities, revenue, coste, extra_prima_p, clicks, wins, ad_starts, first_quartiles, extraprima, mid_views, third_quartiles, completed_views, time_added, last_update, date, idsite, formatloads, product, player) VALUES $Values ;";			
		$db2->query($sql);
		//$db3->query($sql);
	}
				
	echo "Resume Ready \n";
