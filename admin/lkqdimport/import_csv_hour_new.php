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
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie.txt';
	
//	echo $Date = "2022-01-16";
	echo $Date = date('Y-m-d', time() - 1200);
	
	//if($Date == "2021-06-02"){
		
	//	echo "EXIT";
	//	exit(0);
		
	//}
	
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
	
	
	
	$arraySpecialFill = array('Belgium', 'Netherlands', 'Turkey', 'United Kingdom', 'Trinidad and Tobago', 'Jamaica');
	$SpecialFill25 = array('Jamaica');
	$SpecialFill12 = array('Belgium', 'Trinidad and Tobago');
	$SpecialFill6 = array('Turkey');
	$arraySpecialFillWL = array('Italy', 'Portugal', 'Germany', 'Greece', 'Poland');
	$arraySpecialFillWL5 = array('Poland');
	
	/*
	$new = '';
	if($Date != '2021-05-31'){
		$new = '';
	}
	*/
	$BLDomains = array();
	foreach($arraySpecialFill AS $Cntry){
		$CntryFile = str_replace(' ', '', $Cntry);
		//$FileCSV = '/var/www/html/login/admin/lkqdimport/cpm_blacklist'.$new.'/'.$CntryFile.'.csv';
		$FileCSV = '/var/www/html/login/admin/lkqdimport/cpm_blacklist/'.$CntryFile.'.csv';
		
		if(file_exists($FileCSV)){
			$Csv = array_map('str_getcsv', file($FileCSV));
			foreach($Csv as $Co){
				$BLDomains[$Cntry][] = $Co[0];
			}
		}else{
			$BLDomains[$Cntry][] = 'none';
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
		
	//echo "Import $DateFrom to $DateTo - $HFrom $HTo \n";
	//exit(0);
	$StartTime = round(microtime(true) * 1000);
	echo 'Start: ' . $StartTime . "\n";
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
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
	
	
	$ImportData = getHourDataCSV($DateFrom, $DateTo, $HFrom, $HTo);
	
	if($ImportData === false){
		echo "Loggin in... \n\n";
		logIn('Reporting');
		$ImportData = getHourDataCSV($DateFrom, $DateTo, $HFrom, $HTo);
	}
	
	echo 'Import Finish: ' . (round(microtime(true) * 1000) - $StartTime) . " COUNT: " . count($ImportData) . "\n";
	//print_r($ImportData);
	//exit(0);
	
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);

	//4.5 Hoy, 3.5 Viernes, 3 2 semanas
	//1.20, 1.29



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
		
		$sql = "DELETE FROM $TablaName WHERE Date = '$DateFrom' AND Hour BETWEEN '$HFrom' AND '$HTo' AND Manual = 0 AND Player = 1";
		///exit(0);
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
					if($Nn == 7){ $OriginalImpressions = takeComa($Line); }
					//if($Nn == 8){ $CPM = takeMoney($Line); }
					if($Nn == 9){ $Revenue = takeMoney($Line); }
					if($Nn == 10){ $Coste = takeMoney($Line); }
					if($Nn == 11){ $OriginalformatLoads = takeComa($Line); }
					if($Nn == 12){ $OriginalClicks = takeComa($Line); }
					if($Nn == 13){ $OriginalFirstQuartiles = takeComa($Line); }
					if($Nn == 14){ $OriginalMidpoints = takeComa($Line); }
					if($Nn == 15){ $OriginalThirdQuartiles = takeComa($Line); }
					if($Nn == 16){ $OriginalCompletedViews = takeComa($Line); }
					if($Nn == 17){ $adStarts = 0; } //takeComa($Line);
					$Wins = 0;
				}
				$Nn++;
			}
			
			if($N > 0 && $Last === false){
				$timeAdded = time();
				$lastUpdate = time();
				
				$Min = 1.2;
				$Max = 1.32;
				
				$VastPubs = array(
					'VenatusMedia-IB', //56924, //VenatusMedia
					'AgoraPL-AQ', //60889, //AgoraPL
					'playoncontent', //57960, //playoncontent
					'hispanicexchange-MC', //56946, //hispanicexchange-MC
					'Thechronicleherald-LL', //58220, //Thechronicleherald
					'motorsportnetwork-IB', //58049, //motorsportnetwork-IB
					'NPMedia-JC', //60966, //NPMedia
					'CiaoPeopleIT-RC', //61380 //CiaoPeopleIT
					'semseoymasonlinemarketing', //59904 semseoymasonlinemarketing
				);
				
				if($OriginalImpressions > 0){
					$CurrentCPM = $Coste / $OriginalImpressions * 1000;
				}else{
					$CurrentCPM = 0;
				}
				
				$Un = '';
				
				if(!in_array($LKQDuser, $VastPubs)){
					if($CurrentCPM < $Max){
					
						if(intval($TagId) % 2 == 0){
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
					
					$formatLoads = ceil($OriginalformatLoads * 95.5 / 100);
				}else{
					$Un = ' UNTUCHED VAST';
					
					$Impressions = $OriginalImpressions;
					$CompletedViews = $OriginalCompletedViews;
					$Clicks = $OriginalClicks;
					
					$FirstQuartiles = $OriginalFirstQuartiles;
					$Midpoints = $OriginalMidpoints;
					$ThirdQuartiles = $OriginalThirdQuartiles;
					
					$formatLoads = $OriginalformatLoads;
				}
				
				
				//echo "FL $OriginalformatLoads > $formatLoads - Imp $OriginalImpressions > $Impressions ($Coste / $Coef) ORIGINAL CPM: $CurrentCPM - 1Q: $FirstQuartiles 2Q: $Midpoints 3Q: $ThirdQuartiles Comp: $CompletedViews $Un \n";
				$arDate = explode('-', $Date);
				$Day = intval($arDate[2]);
				$Month = intval($arDate[1]);
				
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
				
				if($TagId == 1156952 || $TagId == 1156951){
					//echo "$idSite - $idUser - $idTag";
					//exit(0);
				}
				
				if($inserta == 1){
					if(in_array($Country, $arraySpecialFill)){
						//echo "Is Country $Country - ";
						if(!in_array($Domain, $BLDomains[$Country])){
							
							if(intval($idUser) != 28336){
							
								//echo "$Domain Not in BL - ";
								if($formatLoads >= 2){
									
									if(in_array($Country, $SpecialFill25)){
										//echo "$Country $Date $Hour \n";
										
										if(intval($TagId) % 2 == 0){
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
										
										$Multiplier = (13.5 - $HourI) / 100;
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
										
									}elseif(in_array($Country, $SpecialFill12)){
										if(intval($TagId) % 2 == 0){
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
										
										$Multiplier = (10.25 - $HourI) / 100;
										$Impressions = intval($formatLoads * $Multiplier);
										if($Impressions < 0){
											$Impressions = 0;
										}
										
										$Revenue = $Impressions * 0.0030;
										$Coste = $Revenue * 0.4;
										
										if ($idSite % 2 == 0){
											$VTRValue = 720 - $Day + $Month + $Hour;
										}else{
											$VTRValue = 720 + $Day - $Hour - $Month;
										}
										$VTRValue = $VTRValue / 1000;
										$CompletedViews = intval($Impressions * $VTRValue);
										
									}elseif(in_array($Country, $SpecialFill6)){
										//echo "$Country $Date $Hour \n";
										
										if(intval($TagId) % 2 == 0 && $Domain != 'igrus.com'){
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
										
										$Multiplier = (5.25 - $HourI) / 100;
										$Impressions = intval($formatLoads * $Multiplier);
										if($Impressions < 0){
											$Impressions = 0;
										}
										
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
										if(intval($TagId) % 2 == 0){
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
										
										
										if(($Domain == 'independent.co.uk') && $Country == 'United Kingdom' && ($Date != '2022-03-18' || intval($Hour) >= 14)){
										
											echo $Domain . ': ' . $Date . ':' . $Hour . " -> 130 \n";
											$Multiplier = (130 - $HourI) / 100;
										
										}elseif($Domain == 'independent.co.uk' && $Country == 'United Kingdom'){
											
											echo $Domain . ': ' . $Date . ':' . $Hour . " -> 70 \n";
											$Multiplier = (70 - $HourI) / 100;
											
										}
										
										
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
						}
						//echo "\n";
					}
					
					if(in_array($Country, $arraySpecialFillWL5)){
						//echo "Is Country $Country - ";
						if(in_array($Domain, $WLDomains[$Country])){
							if(intval($idUser) != 28336){
								//echo "$Domain in WL - ";
								if($formatLoads >= 2){
									//echo "$Country $Date $Hour \n";									
									if(intval($TagId) % 2 == 0){
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
									
									$Multiplier = (7.0 - $HourI) / 100;
									$Impressions = intval($formatLoads * $Multiplier);
									if($Impressions < 0){
										$Impressions = 0;
									}
									
									$Revenue = $Impressions * 0.0030;
									$Coste = $Revenue * 0.4;
									
									if ($idSite % 2 == 0){
										$VTRValue = 720 - $Day + $Month + $Hour;
									}else{
										$VTRValue = 720 + $Day - $Hour - $Month;
									}
									$VTRValue = $VTRValue / 1000;
									$CompletedViews = intval($Impressions * $VTRValue);
									//echo "Impressions: $Impressions Revenue: $Revenue Coste: $Coste CV: $CompletedViews ($VTRValue) (Mutiplier X $Multiplier)";
								}
							}
						}
						//echo "\n";
					}elseif(in_array($Country, $arraySpecialFillWL)){
						//echo "Is Country $Country - ";
						if(in_array($Domain, $WLDomains[$Country])){
							if(intval($idUser) != 28336){
								//echo "$Domain in WL - ";
								if($formatLoads >= 2){
									//echo "$Country $Date $Hour \n";									
									if(intval($TagId) % 2 == 0){
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
									
									$Multiplier = (18.5 - $HourI) / 100;
									$Impressions = intval($formatLoads * $Multiplier);
									if($Impressions < 0){
										$Impressions = 0;
									}
									
									$Revenue = $Impressions * 0.0030;
									$Coste = $Revenue * 0.4;
									
									if ($idSite % 2 == 0){
										$VTRValue = 720 - $Day + $Month + $Hour;
									}else{
										$VTRValue = 720 + $Day - $Hour - $Month;
									}
									$VTRValue = $VTRValue / 1000;
									$CompletedViews = intval($Impressions * $VTRValue);
									//echo "Impressions: $Impressions Revenue: $Revenue Coste: $Coste CV: $CompletedViews ($VTRValue) (Mutiplier X $Multiplier)";
								}
							}
						}
						//echo "\n";
					}
					
					
					if($Domain == 'roddelpraat.nl'){
						if($formatLoads > 0){
							$Impressions = ceil($formatLoads / 1.68);
							$Coste = $Impressions * 1.2 / 1000;
							
						}
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
							
							// $sql = "UPDATE report_domain SET name = '$DomainS' WHERE id = $idDomain";
							// $db2->query($sql);
							//$db3->query($sql);
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
										
					$Values .= "$Coma ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '1', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$RevenueEur', '$Coste', '$CosteEur', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Midpoints', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$DateFrom', '$Hour')";
					$Coma = ",";
					if($Nins == 1000){
						//exit(0);
						$sql = "INSERT INTO $TablaName (idUser, idTag, idSite, Domain, Country, Player, Impressions, Opportunities, formatLoads, Revenue, RevenueEur, Coste, CosteEur, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date, Hour) VALUES $Values ;";
						
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
			$sql = "INSERT INTO $TablaName (idUser, idTag, idSite, Domain, Country, Player, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date, Hour) VALUES $Values ;";			
			$db->query($sql);
		}
		
		echo "Hours Imported - LKQD\n";
	//	exit(0);
		
		
		/*
		$CountryArray2 = array();
		$PublisherArray2 = array();
		$DateOwn = date('Y-m-d', time() - 1200);
		
		$sql = "DELETE FROM $TablaName WHERE Date = '$DateOwn' AND Manual = 3";
		$db->query($sql);
		
		$sql = "SELECT * FROM supply_monthly_report WHERE date = '$DateOwn'";
		$query = $db2->query($sql);
		if($db2->num_rows($query) > 0){
			while($Sta = $db2->fetch_array($query)){
				$idSite = $Sta['website_id'];
				$idTag = $Sta['website_zone_id'];
				$Domain = $Sta['domain'];
				$id2Country = $Sta['country_id'];
				$idPublisher = $Sta['publisher_id'];
				$Hour = $Sta['hour'];
				
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
				
				if(array_key_exists($id2Country, $CountryArray2)){
					$idCountry = $CountryArray2[$id2Country];
				}else{
					//$sql = "SELECT iso FROM country WHERE id = '$id2Country' LIMIT 1";
					//$ISO = $db2->getOne($sql);
					
					$sql = "SELECT id FROM reports_country_names WHERE idVidoomy = '$id2Country' LIMIT 1";
					$idCountry = intval($db->getOne($sql));
					
					if($idCountry == 0){
						$idCountry = 999;
					}
					$CountryArray2[$id2Country] = $idCountry;
				}
				
				if(array_key_exists($idPublisher, $PublisherArray2)){
					$idUser = $PublisherArray2[$idPublisher];
				}else{
					$sql = "SELECT user_id FROM publisher WHERE id = '$idPublisher' LIMIT 1";
					$idUser = $db2->getOne($sql);

					$PublisherArray2[$idPublisher] = $idUser;
				}
				
				$Impressions = $Sta['impressions'];
				$Opportunities = 0;
				$formatLoads = $Sta['formatloads'];
				$Revenue = $Sta['usd_revenue'];
				$Coste = $Sta['usd_cost'];
				$ExtraprimaP = 0;
				$Extraprima = 0;
				$Clicks = $Sta['clicks'];
				$Wins = 0;
				$adStarts = $Sta['starts'];
				$FirstQuartiles = $Sta['first_quartiles'];
				$Midpoints = $Sta['mid_points'];
				$ThirdQuartiles = $Sta['third_quartiles'];
				$CompletedViews = $Sta['completes'];
				
				
				$timeAdded = time();
				$lastUpdate = time();
				
				$sql = "INSERT INTO $TablaName (idUser, idTag, idSite, Domain, Country, Impressions, Opportunities, formatLoads, Revenue, Coste, ExtraprimaP, Extraprima, Clicks, Wins, adStarts, FirstQuartiles, MidViews, ThirdQuartiles, CompletedViews, timeAdded, lastUpdate, Date, Hour, Manual)
				 VALUES 
				('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Midpoints', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$DateOwn', '$Hour', '3')";
				$db->query($sql);
				//echo $sql;
				
				$Ni++;
			}
		}
		
		echo "Hours Imported - Own\n";
		*/
		//exit(0);
		
		// CHECK IF
		if($Ni > 0){
			$Subject = 'Hourly Update OK - Viejo ' . $LastU;
			$message = "Actualizacion realizada. $Ni registros insertados. Hour: $HFrom - $HTo Date: $Date ? $DateFrom";
			
			$Nins = 0;
			$Nis = 0;
			$Coma = "";
			$Values = "";
			
			$sql = "DELETE FROM $TablaNameResume WHERE Date = '$DateFrom' AND Player = 1";
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
		    
		    FROM $TablaName WHERE Date = '$DateFrom' AND idUser > 0 AND Player = 1
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
			
				$Values .= "$Coma ('$idUser', '$idTag', '$idSite', '$idDomain', '$idCountry', '1', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$RevenueEur', '$Coste', '$CosteEur', '$ExtraprimaP', '$Extraprima', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$DateFrom')";
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
			
			/*
			
			$mem_var = new Memcached('reps');
			$mem_var->addServer("localhost", 11211);
			$mem_var->flush(1);
			
*/			



			
			
			//exit(0);
			
			
			
			$Nins = 0;
			$Nis = 0;
			$Coma = "";
			$Values = "";
			
			$Countries = array();
			
			$sql = "DELETE FROM $TablaNameResume2 WHERE date = '$DateFrom' AND player = 1";
			$db2->query($sql);
			//$db3->query($sql);
			
			$sql = "SELECT * FROM $TablaNameResume WHERE Date = '$DateFrom' AND idUser > 0 AND idSite > 0 AND Player = 1";
			
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
				
				
				$Values .= "$Coma ('$ID', '$idUser', '$idTag', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$Revenue', '$Coste', '$ExtraprimaP', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Extraprima', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$DateFrom', '$idSite', '$formatLoads', '0', '1')";
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
	
	$mail = new PHPMailer;
								
	$mail->isSMTP();
	$mail->SMTPDebug = 0;
	$mail->Debugoutput = 'html';
	
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 465;
	$mail->SMTPSecure = 'ssl';
	$mail->SMTPAuth = true;
	$mail->Username = "notifysystem@vidoomy.net";
	$mail->Password = "NoTyFUCK05-1";
	$mail->CharSet = 'UTF-8';
	$mail->setFrom('notifysystem@vidoomy.net', 'Vidoomy');
	$mail->addReplyTo('notifysystem@vidoomy.net', 'Vidoomy');
	$mail->addAddress('federico.izuel@vidoomy.com');
	
	$mail->Subject = $Subject;
	$mail->msgHTML($message);
	$mail->send();
	