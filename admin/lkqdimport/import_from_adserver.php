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

	$arraySpecialFill = array('Belgium', 'Netherlands', 'Italy', 'Portugal', 'Germany', 'Turkey', 'South Africa', 'United Kingdom', 'Trinidad and Tobago', 'Jamaica', 'Lithuania', 'Latvia', 'Estonia', 'France');
	$SpecialFill25 = array('Turkey', 'Trinidad and Tobago', 'Jamaica', 'Lithuania', 'Latvia', 'Estonia');

	$arraySpecialFill = array(
		'BE' => 'Belgium', 
		'NL' => 'Netherlands',
		'IT' => 'Italy',
		'PT' => 'Portugal', 
		'DE' => 'Germany',
		'TR' => 'Turkey',
		'ZA' => 'South Africa',
		'GB' => 'United Kingdom',
		'TT' => 'Trinidad and Tobago',
		'JM' => 'Jamaica',
		'LT' => 'Lithuania',
		'LV' => 'Latvia',
		'EE' => 'Estonia',
		'FR' => 'France'
	);
	
	$SpecialFill25 = array(
		'TR' => 'Turkey', 
		'TT' => 'Trinidad and Tobago', 
		'JM' => 'Jamaica', 
		'LT' => 'Lithuania',
		'LV' => 'Latvia',
		'EE' => 'Estonia'
	);
	
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
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$dbuser2 = "root";
	$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$DateDruid = new DateTime();
	$DateDruid->setTimezone(new DateTimeZone('UTC'));
	$DateDruid->modify("-1 hour");
	$Day = intval($DateDruid->format('d'));
	$Month = intval($DateDruid->format('m'));
	echo $DateD1 = $DateDruid->format('Y-m-d H:00:00');
	$DateD2 = $DateDruid->format('Y-m-d H:59:59');
	$DateEST = $DateDruid;
	
	
	$ch = curl_init( 'http://vdmdruidadmin:U9%3DjPvAPuyH9EM%40%26@ec2-3-120-137-168.eu-central-1.compute.amazonaws.com:8888/druid/v2/sql' );
	
	$Query = "SELECT __time, Country, Domain, Zone, SUM(sum_FormatLoads) AS FormatLoads, SUM(sum_Impressions) AS Impressions, SUM(sum_ClickThrus) AS Clicks, SUM(sum_FirstQuartiles) AS FirstQuartiles, SUM(sum_MidPoints) AS MidPoints, SUM(sum_ThirdQuartiles) AS ThirdQuartiles, SUM(sum_VideoCompletes) AS VideoCompletes FROM production_enriched_event_supply WHERE __time >= '$DateD1' AND  __time <= '$DateD2' GROUP BY __time, Country, Domain, Zone ORDER BY 5 DESC";
	$Query . "\n\n";
	//exit(0);
	
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
	
	$DateEST->setTimezone(new DateTimeZone('America/New_York'));
	$HourEST = intval($DateEST->format('H'));
	$DateESTs = $DateEST->format('Y-m-d');
	$sql = "DELETE FROM $TablaName WHERE Date = '$DateESTs' AND Hour BETWEEN '$HourEST' AND '$HourEST' AND Manual = 0 AND Player = 2";
	///exit(0);
	$db->query($sql);
	
	foreach($result as $kres => $res){
		if($kres >= 1){
	
			$Time = $res[0];
			$Country = strtoupper($res[1]);
			$Domain = $res[2];
			$Zone = $res[3];
			$OriginalformatLoads = $res[4];
			$OriginalImpressions = $res[5];
			$OriginalClicks = $res[6];
			
			$OriginalFirstQuartiles = $res[7];
			$OriginalMidpoints = $res[8];
			$OriginalThirdQuartiles = $res[9];
			$OriginalCompletedViews = $res[10];
			
			$adStarts = 0;
			$Wins = 0;
			$Opportunities = 0;
			
			$DateH = new DateTime($Time, new DateTimeZone('UTC'));
			$DateH->setTimezone(new DateTimeZone('America/New_York'));
			$Hour = intval($DateH->format('H'));
			$Date = $DateH->format('Y-m-d');
			
			if(array_key_exists($Zone, $TagsArray)){
				$idTag = $TagsArray[$Zone]['idTag'];
				$idSite = $TagsArray[$Zone]['idSite'];
				$idUser = $TagsArray[$Zone]['idUser'];
			}else{
				$sql = "SELECT id FROM " . TAGS . " WHERE TagName = '$Zone' AND idPlatform = 1 ORDER BY id DESC LIMIT 1";
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
				$TagsArray[$Zone]['idTag'] = $idTag;
				$TagsArray[$Zone]['idSite'] = $idSite;
				$TagsArray[$Zone]['idUser'] = $idUser;
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
			
			$Min = 1.2;
			$Max = 1.32;
			
			if($OriginalImpressions > 0){
				$CurrentCPM = $Coste / $OriginalImpressions * 1000;
			}else{
				$CurrentCPM = 0;
			}
			
			$Un = '';
			
			if($CurrentCPM < $Max){
					
				if(intval($idTag) % 2 == 0){
					if($Hour >= 10){
						$HourI = $Hour / 2; 
						if($HourI >= 10){
							$HourI = $HourI / 2; 
						}
					}else{
						$HourI = $Hour;
					}
				}else{
					if($Hour >= 6){
						$HourI = $Hour / 3;
					}else{
						$HourI = $Hour;
					}									
				}
				
				$Coef = $Min + ($HourI / 100);
				if($Coef > $Max){
					$Coef = $Max;
				}
				
				$Coef = $Coef / 1000;
				
				
				if($OriginalImpressions > 0){
					$VTR = $OriginalCompletedViews / $OriginalImpressions;
					$CTR = $OriginalClicks / $OriginalImpressions;
				
					$Impressions = round($Coste / $Coef);
					$CompletedViews = round($Impressions * $VTR);
					$Clicks = round($Impressions * $CTR);
				
					$FirstQuartiles = calcPercents(25 , $Impressions, $CompletedViews);
					$Midpoints = calcPercents(50 , $Impressions, $CompletedViews);
					$ThirdQuartiles = calcPercents(75 , $Impressions, $CompletedViews);
				}else{
					$Impressions = 0;
					$CompletedViews = 0;
					$Clicks = 0;
					
					$FirstQuartiles = 0;
					$Midpoints = 0;
					$ThirdQuartiles = 0;
				}
			}else{
				$Impressions = $OriginalImpressions;
				$CompletedViews = $OriginalCompletedViews;
				$Clicks = $OriginalClicks;
				
				$FirstQuartiles = $OriginalFirstQuartiles;
				$Midpoints = $OriginalMidpoints;
				$ThirdQuartiles = $OriginalThirdQuartiles;
				
				$Un = ' UNTUCHED HIGH CPM';
			}
			
			$formatLoads = ceil($OriginalformatLoads * 94.5 / 100);
			
			//if(in_array($Country, $arraySpecialFill)){
			if(array_key_exists($Country, $arraySpecialFill)){
				//echo "Is Country $Country - ";
				if(!in_array($Domain, $BLDomains[$Country])){
					//echo "$Domain Not in BL - ";
					if($formatLoads >= 2){
						
						// && ($Hour > 10 || $Date != '2021-04-15'
						 
						if(array_key_exists($Country, $SpecialFill25)){
							//echo "$Country $Date $Hour \n";
							
							if(intval($idTag) % 2 == 0){
								if($Hour >= 10){
									$HourI = $Hour / 2; 
									if($HourI >= 10){
										$HourI = $HourI / 2; 
									}
								}else{
									$HourI = $Hour;
								}
							}else{
								if($Hour >= 6){
									$HourI = $Hour / 3;
								}else{
									$HourI = $Hour;
								}									
							}
							
							$Multiplier = (12.5 - $HourI) / 100;
							$Impressions = intval($formatLoads * $Multiplier);
							
							$Revenue = $Impressions * 0.0030;
							$Coste = $Revenue * 0.4;
							
							if ($idSite % 2 == 0){
								$VTRValue = 720 - $Day + $Month + $Hour;
							}else{
								$VTRValue = 720 + $Day - $Hour - $Month;
							}
							$VTRValue = $VTRValue / 1000;
							$CompletedViews = intval($Impressions * $VTRValue);
							
						}else{
						
							//echo "$formatLoads >= 2 - ";
							if(intval($idTag) % 2 == 0){
								if($Hour >= 10){
									$HourI = $Hour / 2; 
									if($HourI >= 10){
										$HourI = $HourI / 2; 
									}
								}else{
									$HourI = $Hour;
								}
							}else{
								if($Hour >= 6){
									$HourI = $Hour / 3;
								}else{
									$HourI = $Hour;
								}									
							}
							
							$Multiplier = (25 - $HourI) / 100;
							$Impressions = intval($formatLoads * $Multiplier);
							
							$Revenue = $Impressions * 0.0030;
							$Coste = $Revenue * 0.4;
							
							if ($idSite % 2 == 0){
								$VTRValue = 720 - $Day + $Month + $Hour;
							}else{
								$VTRValue = 720 + $Day - $Hour - $Month;
							}
							$VTRValue = $VTRValue / 1000;
							$CompletedViews = intval($Impressions * $VTRValue);
							
						}
						
						//echo "Impressions: $Impressions Revenue: $Revenue Coste: $Coste CV: $CompletedViews ($VTRValue) (Mutiplier X $Multiplier)";
					}
				
				}
				//echo "\n";
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
	
	echo "Hours Imported - LKQD\n";
	
	
	$Nins = 0;
	$Nis = 0;
	$Coma = "";
	$Values = "";
	
	$sql = "DELETE FROM $TablaNameResume WHERE Date = '$Date' AND Player = 2";
	$db->query($sql);
	
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
