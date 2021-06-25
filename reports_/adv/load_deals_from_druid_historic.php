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
	
	$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	
	require('/var/www/html/login/reports_/adv/common.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');
	

	$DateO = new DateTime();
	//$DateTo = $DateO->format('Y-m-d H:59:59');
	$DateTo = '2021-05-26 23:59:59';
	$DateO->modify('-1 hour');
	$DateFrom = $DateO->format('Y-m-d H:00:00');
	$DateFrom = '2021-05-26 00:00:00';
	
	$TotalRev = 0;
	$TotalImp = 0;
	$TotalImpX = 0;
	
	/* 
	$date2 = new DateTime($Date1);
	$date2->modify('-1 day');
	$Date2 = $date2->format('Y-m-d');
	*/
	

	$DemandTags = array();	
	$ActiveDeals = array();
	$CampaingData = array();
	/*
	$sql = "SELECT * FROM campaign WHERE ssp_id = 7 AND status = 1 AND (id = 2355 OR id = 2488 OR id = 2489 OR id = 2170 OR id = 2534 OR id = 2178 OR id = 2135 OR id = 2430 OR id = 2136 OR id = 2352 OR 
		id = 2351 OR id = 2350 OR id = 2360 OR id = 2016 OR id = 2342 OR id = 2256 OR id = 2153 OR id = 2255 OR id = 2343 OR id = 2533 OR id = 2054 OR id = 2345 OR id = 2133 OR id = 2200 OR id = 2018 OR 
		id = 2101 OR id = 2102 OR id = 2134 OR id = 2166 OR id = 2132 OR id = 2199 OR id = 2210 OR id = 2209 OR id = 2314 OR id = 2308 OR id = 2354 OR id = 2160 OR id = 2554 OR id = 2131 OR id = 2493 OR 
		id = 2089 OR id = 2130 OR id = 2071 OR id = 2348 OR id = 2431 OR id = 2188 OR id = 2206 OR id = 2197 OR id = 2056 OR id = 2179 OR id = 2177 OR id = 2195 OR id = 2287 OR id = 2565 OR id = 2196 OR 
		id = 2035 OR id = 2251 OR id = 2208 OR id = 2510 OR id = 2511 OR id = 2594 OR  id >= 2596 
	)";*/
	$sql = "SELECT * FROM campaign WHERE ssp_id = 7 AND status = 1 AND id = 2796";
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		while($Camp = $db3->fetch_array($query)){
			$idCamp = $Camp['id'];
			//$Camp['deal_id'] = "VDMY_CC_10395(1050826-1050825)";
			
			if(strpos($Camp['deal_id'], '-') !== false && strpos($Camp['deal_id'], '(') !== false && strpos($Camp['deal_id'], ')') !== false){
				$arD = explode('(', $Camp['deal_id']);
				$DealID = $arD[0];
			}else{
				$DealID = $Camp['deal_id'];
			}
			
			

			$RebatePercent = $Camp['rebate'];

			if($Camp['cpm'] > 0){
				$CPM = $Camp['cpm'];
			}else{
				$CPM = 0;
			}
			if($Camp['cpv'] > 0){
				$CPV = $Camp['cpv'];
			}else{
				$CPV = 0;
			}
			$Type = $Camp['type'];
			$AgencyId = $Camp['agency_id'];
			
			/*
			$countryId = 999;
			$sql = "SELECT COUNT(*) FROM campaign_country WHERE campaign_id = '$idCamp' ";
			if($db3->getOne($sql) == 1){
				$sql = "SELECT country_id FROM campaign_country WHERE campaign_id = '$idCamp' ";
				$countryId = $db3->getOne($sql);
			}
				
			$CampaingData[$idCamp]['Country'] = $countryId;
			*/
				
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
			
			
			
			$ch = curl_init( 'http://vdmdruidadmin:U9%3DjPvAPuyH9EM%40%26@ec2-3-120-137-168.eu-central-1.compute.amazonaws.com:8888/druid/v2/sql' );
	
			$Query = "SELECT __time, Country, Domain, Device, SUM(sum_BidRequests) AS Requests, SUM(sum_BidResponses) AS Responses, SUM(sum_FirstQuartile) AS FirstQuartile, SUM(sum_Midpoint) AS Midpoint, SUM(sum_ThirdQuartile) AS ThirdQuartile, SUM(sum_Complete) AS Complete, SUM(sum_Impressions) AS Impressions, SUM(sum_Vimpression) AS VImpressions, SUM(sum_Clicks) AS Clicks, SUM(sum_Money) AS Money FROM prd_rtb_event_production_1	WHERE __time >= '$DateFrom' AND  __time <= '$DateTo' AND Deal = '$DealID' GROUP BY __time, Deal, Country, Domain, Device ORDER BY 1 DESC";
			
			//echo $Query . "\n";
			$DruidStart = time();

			$context = new \stdClass();
			$context->sqlOuterLimit = 30000;
			
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
			
			$DruidDuration = time() - $DruidStart;
			echo "Druid took: $DruidDuration seconds, ";
			//print_r($result);
			
			$InsertionStart = time();
			foreach($result as $Row){
				if($Row[0] != '__time'){
					$Time = $Row[0];
					$Country = $Row[1];
					$Domain = $Row[2];
					$Device = $Row[3];
					$Requests = $Row[4];
					$Bids = $Row[5];
					$Complete25 = $Row[6];
					$Complete50 = $Row[7];
					$Complete75 = $Row[8];
					$CompleteV = $Row[9];
					$Impressions = $Row[10];
					$VImpressions = $Row[11];
					$Clicks = $Row[12];
					//$Money = $result[1][10];
					
					if($Device == 'DT'){
						$Device = 'Desktop';
					}else{
						$Device = 'Mobile';
					}
					
					$RowDate = new DateTime($Time);
					$Hour = $RowDate->format('H');
					$Date = $RowDate->format('Y-m-d');
					
					if($Impressions > 0 && $CPM > 0){
						$Revenue = $Impressions * $CPM / 1000;
					}elseif($CompleteV > 0 && $CPV > 0){
						$Revenue = $CompleteV * $CPV;
					}else{
						$Revenue = 0;
					}
					
					if($RebatePercent > 0 && $Revenue > 0){
						$Rebate = $Revenue * $RebatePercent / 100;
					}else{
						$Rebate = 0;
					}
					
					$sql = "SELECT id FROM country WHERE iso = '$Country' LIMIT 1";
					$idCountry = $db->getOne($sql);
					
					$CompleteVPerc = 0;
					
					$sql = "SELECT id FROM reports_deals WHERE idCampaing = $idCamp AND Domain LIKE '$Domain' AND Device LIKE '$Device' AND idCountry = $idCountry AND Date = '$Date' AND Hour = '$Hour'  LIMIT 1";
					$idStat = $db->getOne($sql);

					
					if(intval($idStat) == 0){
						$sql = "INSERT INTO reports_deals
						(Domain, Device, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, CompleteVPer, Rebate, Date, Hour) 
						VALUES ('$Domain', '$Device', $idCamp, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', '$Complete75', '$CompleteVPerc', $Rebate, '$Date', '$Hour')";
						$db->query($sql);
						//echo $sql . "\n";	
					}else{
						
						$sql = "UPDATE reports_deals SET 
						Requests = $Requests,
						Bids = $Bids, 
						Impressions = $Impressions, 
						Revenue = '$Revenue', 
						VImpressions = $VImpressions, 
						Clicks = $Clicks, 
						CompleteV = $CompleteV, 
						Complete25 = $Complete25, 
						Complete50 = $Complete50, 
						Complete75 = $Complete75, 
						Rebate = $Rebate
						WHERE id = $idStat LIMIT 1";
						$db->query($sql);
					}
				}
			}
			
			$InsertionDuration = time() - $InsertionStart;
			
			echo "Insertion took: $InsertionDuration \n";
			
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	