<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/reports_/adv/config.php');
	//require('/var/www/html/login/reports_/adv/config_pre.php');
	require('/var/www/html/login/db.php');
	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	//exit(0);
	//$db2 = new SQL($advPre['host'], $advPre['db'], $advPre['user'], $advPre['pass']);
	$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	
	require('/var/www/html/login/reports_/adv/common.php');
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

	//$Date = date('Y-m-d', time() - (3600 * 4));
	$Date = '2021-11-29';
	//$Hour = date('H');
	$Hour = 23;
	
	$TotalRev = 0;
	$TotalImp = 0;
	$TotalImpX = 0;
	
	/* 
	$date2 = new DateTime($Date1);
	$date2->modify('-1 day');
	$Date2 = $date2->format('Y-m-d');
	*/
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie.txt';

	$DemandTags = array();	
	$ActiveDeals = array();
	$CampaingData = array();
	$sql = "SELECT * FROM campaign WHERE id = 5652"; //ssp_id = 4 AND status = 1
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		while($Camp = $db3->fetch_array($query)){
			$idCamp = $Camp['id'];
			
			$ActiveDeals[$idCamp] = $Camp['deal_id'];
			$DemandTags[] = $Camp['deal_id'];
			
			$CampaingData[$idCamp]['DealId'] = $Camp['deal_id'];
			$CampaingData[$idCamp]['Rebate'] = $Camp['rebate'];
			$CampaingData[$idCamp]['Type'] = $Camp['type'];
			if($Camp['cpm'] > 0){
				$CampaingData[$idCamp]['CPM'] = $Camp['cpm'];
			}else{
				$CampaingData[$idCamp]['CPM'] = 0;
			}
			if($Camp['cpv'] > 0){
				$CampaingData[$idCamp]['CPV'] = $Camp['cpv'];
			}else{
				$CampaingData[$idCamp]['CPV'] = 0;
			}
			$CampaingData[$idCamp]['Type'] = $Camp['type'];
			$CampaingData[$idCamp]['AgencyId'] = $Camp['agency_id'];
			
			$countryId = 999;
			$sql = "SELECT COUNT(*) FROM campaign_country WHERE campaign_id = '$idCamp' ";
			if($db3->getOne($sql) == 1){
				$sql = "SELECT country_id FROM campaign_country WHERE campaign_id = '$idCamp' ";
				$countryId = $db3->getOne($sql);
			}
			
			$CampaingData[$idCamp]['Country'] = $countryId;
			
			if($Camp['vtr_from'] > 0 && $Camp['vtr_to'] > 0){
				$CampaingData[$idCamp]['VTRFrom'] = $Camp['vtr_from'];
				$CampaingData[$idCamp]['VTRTo'] = $Camp['vtr_to'];
				$CampaingData[$idCamp]['CVTR'] = true;
			}else{
				$CampaingData[$idCamp]['CVTR'] = false;
			}
				
			if($Camp['ctr_from'] > 0 && $Camp['ctr_to'] > 0){
				$CampaingData[$idCamp]['CTRFrom'] = $Camp['ctr_from'];
				$CampaingData[$idCamp]['CTRTo'] = $Camp['ctr_to'];
				$CampaingData[$idCamp]['CCTR'] = true;
			}else{
				$CampaingData[$idCamp]['CCTR'] = false;
			}
			
			if($Camp['viewability_from'] > 0 && $Camp['viewability_to'] > 0){
				$CampaingData[$idCamp]['ViewFrom'] = $Camp['viewability_from'];
				$CampaingData[$idCamp]['ViewTo'] = $Camp['viewability_to'];
				$CampaingData[$idCamp]['CView'] = true;
			}else{
				$CampaingData[$idCamp]['CView'] = false;
			}
		}
	}
	
	
	//print_r($ActiveDeals);
	//exit();
	
	//$ActiveDeals = array(204 => '1029948', 205 => '1029605', 206 => '1028475');
	//$ActiveDeals = array(290 => '1035009');
	
	$ImportData = getAdvertiserDemandReportCSV($Date, $ActiveDeals, 0, $Hour);

	if($ImportData === false){
		//echo "Loggin in... \n\n";
		logIn();
		$ImportData = getAdvertiserDemandReportCSV($Date, $ActiveDeals, 0, $Hour);
	}
	//print_r($ImportData);
	//exit(0);
	$Bids = 0;
	
	if($ImportData !== false){
		$N = 0;
		$Last = false;
		foreach($ImportData as $DataK => $DataL){
			$Nn = 0;
			foreach($DataL as $Line){
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
					if($Nn == 1){ $TagId = $Line; }
					if($Nn == 3){ $Requests = takeComa($Line); }
					if($Nn == 4){ $Impressions = takeComa($Line); }
					if($Nn == 5){ $VImpressions = takeComa($Line); }
					if($Nn == 6){ $CompleteV = takeComa($Line); }
					if($Nn == 7){ $Clicks = takeComa($Line); }
					if($Nn == 8){ $Revenue = takeMoney(takeComa($Line)); }
					if($Nn == 9){ $Complete25 = takeComa($Line); }
					if($Nn == 10){ $Complete50 = takeComa($Line); }
					if($Nn == 11){ $Complete75 = takeComa($Line); }
				}
				$Nn++;
			}

			//exit(0);
			if($N > 0 && $Last === false){
				//echo $Hour . "\n";
				if(in_array($TagId, $ActiveDeals)){
					//echo $idCampaing . "\n";
					//echo $TagId . "\n";
					$idCampaing = array_search($TagId, $ActiveDeals);
					
					$RebatePercent = $CampaingData[$idCampaing]['Rebate'];
					$DealID = $CampaingData[$idCampaing]['DealId'];
					$idCountry = $CampaingData[$idCampaing]['Country'];
					$Type = $CampaingData[$idCampaing]['Type'];
					$CPM = $CampaingData[$idCampaing]['CPM'];
					$CPV = $CampaingData[$idCampaing]['CPV'];
					$AgencyId = $CampaingData[$idCampaing]['AgencyId'];
					
					$CVTR = $CampaingData[$idCampaing]['CVTR'];
					$CCTR = $CampaingData[$idCampaing]['CCTR'];
					$CView = $CampaingData[$idCampaing]['CView'];
					
					if($RebatePercent > 0 && $Revenue > 0){
						$Rebate = $Revenue * $RebatePercent / 100;
					}else{
						$Rebate = 0;
					}
					
					//print_r($CampaingData[$idCampaing]);
					//exit();
					$CompleteVPerc = 0;
					
					/*
					$RandVI = rand(7300,7500)/10000; //Cheil Panama
					$RandVI2 = rand(7400,7800)/10000; //RR_GrupoP
					$RandVI3 = rand(3900,4200)/10000; //AM_adbid_CO_Claro_ParaTiPrimero_Marzo
					$RandVI4 = rand(3400,3800)/10000; //AM_adbid_CO_Claro_EstrategiaDigital
					$RandVI5 = rand(7100,7400)/10000;
					$RandVI6 = rand(7100,7300)/10000; //MediacaOnline_BR_MOL_Video1_75%_completes - Dickens_Prudence_MX_10
					$RandVI7 = rand(7100,7300)/10000; //MediacaOnline_BR_MOL_Video2_25%_completes	
					*/
					
					$sql = "SELECT id FROM reports WHERE SSP = 4 AND idCampaing = $idCampaing AND Date = '$Date' AND Hour = '$Hour' LIMIT 1";
					$idStat = $db->getOne($sql);
					
					//if($Date != '2020-06-16' || $Hour >= 15){
						if(intval($idStat) == 0){
							if($Type == 2){
								if($CCTR === true){
									$CTRFrom = $CampaingData[$idCampaing]['CTRFrom'] * 100;
									$CTRTo = $CampaingData[$idCampaing]['CTRTo'] * 100;
									
									$RandCTR = rand($CTRFrom, $CTRTo) / 10000;
									$Clicks = intval($Impressions * $RandCTR);
								}
								
								if($CVTR === true){
									$VTRFrom = $CampaingData[$idCampaing]['VTRFrom'] * 100;
									$VTRTo = $CampaingData[$idCampaing]['VTRTo'] * 100;
									
									$RandVTR = rand($VTRFrom, $VTRTo) / 10000;
									$CompleteV = intval($Impressions * $RandVTR);
									$CompleteVPerc = $RandVTR;
										
									$Complete25 = calcPercents(25, $Impressions, $CompleteV);
									$Complete50 = calcPercents(50, $Impressions, $CompleteV);
									$Complete75 = calcPercents(75, $Impressions, $CompleteV);
								}
								
								if($CView === true){
									$ViewFrom = $CampaingData[$idCampaing]['ViewFrom'] * 100;
									$ViewTo = $CampaingData[$idCampaing]['ViewTo'] * 100;
									
									$RandView = rand($ViewFrom, $ViewTo) / 10000;
									$VImpressions = intval($Impressions * $RandView);
								}
								
								if($Impressions > 0 && $CPM > 0){
									$Revenue = $Impressions * $CPM / 1000;
								}elseif($CompleteV > 0 && $CPV > 0){
									$Revenue = $CompleteV * $CPV;
								}else{
									$Revenue = 0;
								}
							}
							
							$sql = "INSERT INTO reports
							(SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, CompleteVPer, Rebate, Date, Hour) 
							VALUES (4, $idCampaing, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', '$Complete75', '$CompleteVPerc', $Rebate, '$Date', '$Hour')";
							$db->query($sql);
							echo $sql . "\n";
							
						}else{
							if($Type == 2){
								$Impressions = intval($Impressions);
					
								$sql = "SELECT Impressions FROM reports WHERE id = $idStat LIMIT 1";
								$ExistingImpressions = $db->getOne($sql);
								
								$arD = explode('-',$Date);
								$NewImpressions = $Impressions - $ExistingImpressions;
								if($NewImpressions > 0){// ($CPM > 0 || $CPV > 0)
									
									if($CCTR === true){
										$CTRFrom = $CampaingData[$idCampaing]['CTRFrom'] * 100;
										$CTRTo = $CampaingData[$idCampaing]['CTRTo'] * 100;
										
										$RandCTR = rand($CTRFrom, $CTRTo) / 10000;
										$Clicks = intval($Impressions * $RandCTR);
									}
									
									if($CVTR === true){
										$sql = "SELECT CompleteVPer FROM reports WHERE id = '$idStat' LIMIT 1";
										$CompleteVPerc = $db->getOne($sql);
										
										if($CompleteVPerc > 0){
											$RandVTR = $CompleteVPerc;
										}else{
											$VTRFrom = $CampaingData[$idCampaing]['VTRFrom'] * 100;
											$VTRTo = $CampaingData[$idCampaing]['VTRTo'] * 100;
											$RandVTR = rand($VTRFrom, $VTRTo) / 10000;
											$CompleteVPerc = $RandVTR;
										}
										
										$CompleteV = intval($Impressions * $RandVTR);
	
										$Complete25 = calcPercents(25, $Impressions, $CompleteV);
										$Complete50 = calcPercents(50, $Impressions, $CompleteV);
										$Complete75 = calcPercents(75, $Impressions, $CompleteV);
									}
									
									if($CView === true){
										$ViewFrom = $CampaingData[$idCampaing]['ViewFrom'] * 100;
										$ViewTo = $CampaingData[$idCampaing]['ViewTo'] * 100;
										
										$RandView = rand($ViewFrom, $ViewTo) / 10000;
										$VImpressions = intval($Impressions * $RandView);
									}
									
									if($CPM > 0){
										$AddRevenue = $NewImpressions * $CPM / 1000;
									}elseif($CPV > 0){
										$sql = "SELECT CompleteV FROM reports WHERE id = $idStat LIMIT 1";
										$ExistingCompleteV = $db->getOne($sql);
										
										$NewCompleteV = $CompleteV - $ExistingCompleteV;
										
										$AddRevenue = $NewCompleteV * $CPV;
									}else{
										$AddRevenue = 0;
									}
									
									$Revenue = "Revenue + $AddRevenue";
									$sql = "UPDATE reports SET 
									Requests = $Requests, 
									Bids = $Bids, 
									Impressions = $Impressions, 
									Revenue = $Revenue, 
									VImpressions = $VImpressions,
									Clicks = $Clicks,
									CompleteV = $CompleteV,
									Complete25 = $Complete25,
									Complete50 = $Complete50,
									Complete75 = $Complete75,
									CompleteVPer = $CompleteVPerc,
									Rebate = $Rebate
									
								WHERE id = '$idStat' LIMIT 1";
									
									$db->query($sql);
									echo $sql . "\n";
								}else{
									//echo "No New I CPM $CPM \n";
								}
							}else{
								$sql = "UPDATE reports SET 
								Requests = $Requests, 
								Bids = $Bids, 
								Impressions = $Impressions, 
								Revenue = $Revenue, 
								VImpressions = $VImpressions,
								Clicks = $Clicks,
								CompleteV = $CompleteV,
								Complete25 = $Complete25,
								Complete50 = $Complete50,
								Complete75 = $Complete75,
								CompleteVPer = $CompleteVPerc,
								Rebate = $Rebate
							WHERE id = '$idStat' LIMIT 1";
								
								$db->query($sql);
								echo $sql . "\n";
							}
						}
					//}
					//$db->query($sql);
					//echo $sql . "\n";
				}
			}
			$N++;
		}

		$Subject = 'OK 1';
		$message = 'Actualizacion realizada.';
		
	}	
	
	$Date = date('Y-m-d', time() - 3600);
	//updateReportCards($db3, $Date);
	//updateReportCards($db2, $Date);