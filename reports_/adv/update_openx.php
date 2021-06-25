<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/reports_/adv/config.php');
	require('/var/www/html/login/db.php');
	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);

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

	$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	
	require_once '/var/www/html/login/reports_/adv/ODS-PHP-API-Client/OX_ODS_API.php';

	//$uri      = 'http://myopenx-ui.com';
	$uri = 'http://vidoomy-ui3.openxenterprise.com/';
	$email    = 'programmatic@vidoomy.com';
	$password = 'VidApi_8172d';
	$key      = 'fa2096872aec63e1aa851d60bd61588a4db43a5d';
	$secret   = '83983def6b8edfffd8b4339d2800bf22feea9cf4';
	$realm    = 'vidoomy';
	
	$client = new OX_ODS_API($uri, $email, $password, $key, $secret, $realm);
	
	$JsonDateRange = array(
		'attributes' => array(
			array('id' => 'hour'),
			array('id' => 'day'),
			array('id' => 'privateMarketplacePublisherDealId'),
		),
	    'metrics' => array(
			array('id' => 'marketRequests'), 
			array('id' => 'allRequests'), 
			array('id' => 'marketImpressions'),
			array('id' => 'completeFirstOccurance'),
			array('id' => 'firstQuartileFirstOccurance'),
			array('id' => 'midPointFirstOccurance'),
			array('id' => 'thirdQuartileFirstOccurance'),
			array('id' => 'marketPublisherRevenue'),
			array('id' => 'privateMarketPublisherGrossRevenue'),
			array('id' => 'clicks'),
			array('id' => 'exchangeFills')
		)
	);
	
	$encodedJsonDateRange = json_encode($JsonDateRange);

	$report = $client->post('/date-range/', $encodedJsonDateRange, true);
	
	$getBodyData = $report->getBody();
	$DateRageData =json_decode($getBodyData);
	
	//print_r($DateRageData);
	
	$EndDate = $DateRageData->dateRange->maxEndDate;
	//$EndDate = '2020-10-01T23:00:00Z';
	
	//exit(0);
	
	$Date = date('Y-m-d', time() - 3600 * 24);//
	//$Date = '2020-10-01';
	
	//echo date('H');
	//exit(0);
	
	$ActiveDeals = array();
	$CampaingData = array();
	$sql = "SELECT * FROM campaign WHERE ssp_id = 5 AND status = 1";
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		while($Camp = $db3->fetch_array($query)){
			$idCamp = $Camp['id'];
			
			$ActiveDeals[$idCamp] = $Camp['deal_id'];
			$DemandTags[] = $Camp['deal_id'];
			
			$CampaingData[$idCamp]['DealId'] = $Camp['deal_id'];
			$CampaingData[$idCamp]['Rebate'] = $Camp['rebate'];
			$CampaingData[$idCamp]['Type'] = $Camp['type'];
			$CampaingData[$idCamp]['AgencyId'] = $Camp['agency_id'];
			
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
	//exit(0);
	
	$myJson = array(
		'startDate' => $Date . 'T00:00:01Z',
		'endDate' => $EndDate,
		'attributes' => array(
			//array('id' => 'publisherSiteId'),
			array('id' => 'hour'),
			array('id' => 'day'),
			array('id' => 'privateMarketplacePublisherDealId'),
			
		   ),
		'metrics' => array(
			array('id' => 'marketRequests'), 
			array('id' => 'allRequests'), 
			array('id' => 'marketImpressions'),
			array('id' => 'completeFirstOccurance'),
			array('id' => 'firstQuartileFirstOccurance'),
			array('id' => 'midPointFirstOccurance'),
			array('id' => 'thirdQuartileFirstOccurance'),
			array('id' => 'marketPublisherRevenue'),
			array('id' => 'privateMarketPublisherGrossRevenue'),
			array('id' => 'clicks'),
			array('id' => 'exchangeFills')
		)
	);


	$encoded_json = json_encode($myJson);
	
	//echo $encoded_json;
	
	$report = $client->post('/report/', $encoded_json, true);
	
	$getBodyData = $report->getBody();
	
	$prettyData = json_encode(json_decode($getBodyData), JSON_PRETTY_PRINT);
	//echo $prettyData;
	//exit(0);
	
	$Data = json_decode($getBodyData);
	
	foreach($Data->reportData as $D){
		$TagId = $D->privateMarketplacePublisherDealId;
		
		if(in_array($TagId, $ActiveDeals)){
			$idCampaing = array_search($TagId, $ActiveDeals);
			//echo $idCampaing . "\n";
			
			$Requests = $D->allRequests;
			$Impressions = $D->marketImpressions;
			$VImpressions = 0;//$D->marketPublisherRevenueInPCoin;
			$Bids = 0;
			$CompleteV = $D->completeFirstOccurance;
			$Clicks = $D->clicks;
			//$Revenue = $D->marketPublisherRevenueInPCoin;
			$Revenue = $D->privateMarketPublisherGrossRevenueInPCoin;
			$Complete25 = $D->firstQuartileFirstOccurance;
			$Complete50 = $D->midPointFirstOccurance;
			$Complete75 = $D->thirdQuartileFirstOccurance;
			
			$RandVI = rand(8400,8800)/10000;
			$VImpressions = $Impressions * $RandVI;
			
			$arDateH = explode('T', $D->hour);
			$Date = $arDateH[0];
			$Hour = intval($arDateH[1]);
			
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
						
			$CompleteVPerc = 0;
			
			$sql = "SELECT id FROM reports WHERE SSP = 5 AND idCampaing = $idCampaing AND Date = '$Date' AND Hour = '$Hour' LIMIT 1";
			$idStat = $db->getOne($sql);

			if(intval($idStat) == 0){
				
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
				
				if($RebatePercent > 0 && $Revenue > 0){
					$Rebate = $Revenue * $RebatePercent / 100;
				}else{
					$Rebate = 0;
				}
					
				$sql = "INSERT INTO reports
				(SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, CompleteVPer, Rebate, Date, Hour) 
				VALUES (5, $idCampaing, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', '$Complete75', '$CompleteVPerc', $Rebate, '$Date', '$Hour')";
				$db->query($sql);
				//echo $sql . "\n";
				
			}else{
				$Impressions = intval($Impressions);
	
				$sql = "SELECT Impressions FROM reports WHERE id = $idStat LIMIT 1";
				$ExistingImpressions = $db->getOne($sql);
				
				$sql = "SELECT Revenue FROM reports WHERE id = $idStat LIMIT 1";
				$ExistingRevenue = $db->getOne($sql);
				
				$DifRev = $Revenue - $ExistingRevenue;
				
				$arD = explode('-',$Date);
				$NewImpressions = $Impressions - $ExistingImpressions;
				
				if($NewImpressions > 0 || $DifRev >= 0.1){
					
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
					
					/*
					if($CPM > 0){
						$AddRevenue = $NewImpressions * $CPM / 1000;
					}elseif($CPV > 0){
						$sql = "SELECT CompleteV FROM reports WHERE id = $idStat LIMIT 1";
						$ExistingCompleteV = $db->getOne($sql);
						
						$NewCompleteV = $CompleteV - $ExistingCompleteV;
						
						$AddRevenue = $NewCompleteV * $CPV;
					}else{*/
						$AddRevenue = $Revenue - $ExistingRevenue;
					//}
					
					if($AddRevenue < 0.1){
						$AddRevenue = 0;
					}
					
					if($RebatePercent > 0 && $AddRevenue > 0){
						$AddRebate = $AddRevenue * $RebatePercent / 100;
					}else{
						$AddRebate = 0;
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
						Rebate = Rebate + $AddRebate
						
					WHERE id = '$idStat' LIMIT 1";
					
					$db->query($sql);
					//echo $sql . "\n";
				}else{
					//echo "No New I CPM $CPM \n";
				}
			}
			
			
		}
		
	}

?>
