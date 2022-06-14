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
	
	$Date = '2021-11-08';
	$Hour1 = '08';
	$Hour2 = $Hour1;
			
	$ch = curl_init( $druidUrl );
	/*
	$Query = "SELECT __time, Country, SUM(sum_BidRequests) AS Requests, SUM(sum_BidResponses) AS Responses, SUM(sum_FirstQuartile) AS FirstQuartile, SUM(sum_Midpoint) AS Midpoint, SUM(sum_ThirdQuartile) AS ThirdQuartile, SUM(sum_Complete) AS Complete, SUM(sum_Impressions) AS Impressions, SUM(sum_Vimpression) AS VImpressions, SUM(sum_Clicks) AS Clicks, SUM(sum_Money) AS Money FROM prd_rtb_event_production_1 WHERE __time >= '$Date $Hour1:00:00' AND  __time <= '$Date $Hour2:00:00' AND AdType = 'Display' GROUP BY __time, Country ORDER BY 3 DESC LIMIT 1";
	*/
	$Query = "SELECT __time, Country, SspId, PublisherId, Domain, ZoneId, Device, Os, Wseat, Dsp, AdSize, Crid, Adomain, Category, 
SUM(sum_BidRequests) AS Requests, SUM(sum_BidResponses) AS Responses, SUM(sum_Opportunity) AS Opportunity, 
SUM(sum_Vpaid) AS Vpaid, SUM(sum_Impressions) AS Impressions, SUM(sum_Vimpression) AS VImpressions,
SUM(sum_Uimpression) AS Uimpression, SUM(sum_Blocked) AS sum_Blocked, SUM(sum_Clicks) AS Clicks, 
SUM(sum_Money) AS Money, SUM(sum_RMoney) AS RMoney, SUM(sum_PMoney) AS PMoney, 
SUM(sum_BidsUnanswered) AS BidsUnanswered, SUM(sum_BidsWon) AS BidsWon, SUM(sum_BidError) AS BidError,
SUM(sum_BidsTimedOut) AS BidsTimedOut, SUM(sum_BidsFiltered) AS BidsFiltered
FROM prd_rtb_event_production_1 
WHERE __time >= '$Date $Hour1:00:00' AND  __time <= '$Date $Hour2:00:00' AND AdType = 'Display' 
GROUP BY __time, Country,  SspId, PublisherId, Domain, ZoneId, Device, Os, Wseat, Dsp, AdSize, Crid, Adomain, Category";
	//echo $Query . "\n\n";
	//exit(0);
	
	$context = new \stdClass();
	$context->sqlOuterLimit = 300000000;
	
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
	$resultT = json_decode($result) ;
			
	//print_r($result);
	//exit(0);
	$addImps = 0;
	
	$Int = 0;
	$Jsons = "";
	
	if(array_key_exists(1, $resultT)){
		foreach($resultT as $result){
			$Time = $result[0];
			
			$Int++;
			
			if($Time != '__time'){
				$Body = new \stdClass();
				$Body->publisher = "";
				$Body->publisherId = $result[3];
				$Body->sspId = $result[2];
				$Body->zoneId = $result[5];
				$Body->site = "";
				$Body->bundle = "";
				$Body->dbundle = "";
				$Body->adType = "Display";
				$Body->domain = $result[4];
				$Body->device = $result[6];
				$Body->country = $result[1];
				$Body->os = $result[7];
				$Body->wseat = $result[8];
				$Body->deal = "";
				$Body->dsp = $result[9];
				$Body->adSize = $result[10];
				$Body->gdpr = 0;
				$Body->gdprcs = 0;
				$Body->sync = 0;
				$Body->crid = $result[11];
				$Body->adomain = $result[12];
				$Body->category = $result[13];
				$Body->vimpression = $result[19];
				$Body->uimpression = $result[20];
				$Body->firstQuartile = 0;
				$Body->midpoint = 0;
				$Body->thirdQuartile = 0;
				$Body->complete = 0;
				$Body->close = 0;
				$Body->pause = 0;
				$Body->blocked = $result[21];
				$Body->blockedReason = "";
				$Body->bidRequestAvoided = 0;
				$Body->bidAvoidedReason = "";
				$Body->vpaid = $result[17];
				$Body->opportunity = $result[16];
				$Body->bidRequests = $result[14];
				$Body->bidResponses = $result[15];
				$Body->bidsUnanswered = $result[26];
				$Body->bidsTimedOut = $result[29];
				$Body->bidsFiltered = $result[30];
				$Body->bidFilteredReason = "";
				$Body->bidsWon = $result[27];
				$Body->bidsError = $result[28];
				$Body->impressions = $result[18];
				$Body->clicks = $result[22];
				$Body->money = $result[23];
				$Body->pMoney = $result[24];
				$Body->rMoney = $result[25];
				$Body->createdAt = strtotime('2021-11-09 '.$Hour1.':00:00');
				
				$addImps += $result[18];
				
				$Json = new \stdClass();
				
				$Json->type = "rtb.event.enriched";
				$Json->key = "i6586pmtekplt$Int";
				$Json->body = $Body;
				$Json->version = "1.0";
				$Json->meta = null;
				
				$Jsons .= json_encode($Json) . "\n";
				
			}
		}
	}
	
	echo $addImps;
	//echo $Jsons;
	file_put_contents("/var/www/html/login/reports_/adv/druidregen/$Date-$Hour1.json", $Jsons);