<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/reports_/adv/config.php');
	require('/var/www/html/login/db.php');
	require('../../config.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);

	//$db2 = new SQL($advPre['host'], $advPre['db'], $advPre['user'], $advPre['pass']);
	$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	
	require('/var/www/html/login/reports_/adv/common.php');
	
	$DateS = date('2020-07-26');
	
//for($Dd = 1; $Dd <= 20; $Dd = $Dd + 2){
	
	//$Hour = date('H', time() - 3600);
	
	$date1 = new DateTime($DateS);
//	$date1->modify('+' . $Dd . ' days');
	$Date1 = $date1->format('Y-m-d');
	
	$date2 = new DateTime($Date1);
	$date2->modify('-1 day');
	$Date2 = $date2->format('Y-m-d');

	//echo "$Date2|$Date1 \n";
	

	$sql = "SELECT * FROM campaign WHERE ssp_id = 2 AND status = 1";
	$queryC = $db3->query($sql);
	if($db3->num_rows($queryC) > 0){
		while($Camp = $db3->fetch_array($queryC)){
			//print_r($Camp);
			$idCampaing = $Camp['id'];
			$RebatePercent = $Camp['rebate'];
			$DealID = $Camp['deal_id'];
			
			$idCountry = 999;
			$sql = "SELECT COUNT(*) FROM campaign_country WHERE campaign_id = '$idCampaing' ";
			if($db->getOne($sql) == 1){
				$sql = "SELECT country_id FROM campaign_country WHERE campaign_id = '$idCampaing' ";
				$idCountry = $db->getOne($sql);
			}
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://publisher-api.spotxchange.com/1.1/token",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => "client_id=API-cHVibGlzaGVyLzIxODQ0Mw%3D%3D&client_secret=ed6b589134ac4d499e1cc969d6aa193c6d34edab&grant_type=refresh_token&refresh_token=251b3eb9916786f8220f35aa8cfe2572def5391e",
			  CURLOPT_HTTPHEADER => array(
			    "Content-Type: application/x-www-form-urlencoded"
			  ),
			));
			
			$Json = curl_exec($curl);
			
			curl_close($curl);
			
			$Decoded = json_decode($Json);
			$Token = $Decoded->value->data->access_token;
			
			//exit(0);
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://api.spotxchange.com/1.1/Publisher(218443)/Deal($DealID)/DealReport?date_range=$Date2|$Date1",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			  CURLOPT_HTTPHEADER => array(
			    "Authorization: Bearer $Token"
			  ),
			));
			
			$Json = curl_exec($curl);
			curl_close($curl);
			
			echo "$Date2|$Date1 \n";
			
			$Decoded = json_decode($Json);
			//print_r($Decoded);
			//exit(0);

			if(!property_exists($Decoded, 'error')){
				if(is_array($Decoded->value)){
					$DValueDate = $Decoded->value;
				}else{
					$DValueDate = $Decoded->value->data;
				}
				
				foreach($DValueDate as $Deal){
	
					$Requests = $Deal->total_requests;
					$Bids = $Deal->total_responses;
					$Revenue = $Deal->revenue;
					$Impressions = $Deal->impressions;
					$Clicks = $Deal->clicks;
					$CompleteV = intval($Deal->cvr * $Impressions);
					
					//print_r($Deal);
					//exit(0);
					
					$Date = $Deal->date;
					
					$HourI = 23;
					
					$sql = "SELECT
						SUM(Requests) AS Requests,
						SUM(Bids) AS Bids,
						SUM(Revenue) AS Revenue,
						SUM(Impressions) AS Impressions,
						SUM(VImpressions) AS VImpressions,
						SUM(Clicks) AS Clicks,
						SUM(CompleteV) AS CompleteV
						FROM reports WHERE SSP = 2 AND Date = '$Date' AND idCampaing = '$idCampaing'";
						
					$query2 = $db->query($sql);
					$W = $db->fetch_array($query2);
					
					$Requests = $Requests - $W['Requests'];
					$Bids = $Bids - $W['Bids'];
					
					if($Bids < 0){
						$Bids = 0;
					}
					
					$Revenue = $Revenue - $W['Revenue'];
					$Impressions = $Impressions - $W['Impressions'];
					$RandVI = rand(8000,8600)/10000;
					$VImpressions = $Impressions * $RandVI;
					if($VImpressions < 0){ $VImpressions = 0; }
					$Clicks = $Clicks - $W['Clicks'];
					$CompleteV = $CompleteV - $W['CompleteV'];
					
					if($RebatePercent > 0 && $Revenue > 0){
						$Rebate = $Revenue * $RebatePercent / 100;
					}else{
						$Rebate = 0;
					}
					
					$sql = "SELECT id FROM reports WHERE SSP = 2 AND idCampaing = $idCampaing AND Date = '$Date' AND Hour = '$HourI' LIMIT 1";//AND idCountry = $idCountry 
					$idStat = $db->getOne($sql);
					
					if(intval($idStat) == 0){
						$sql = "INSERT INTO reports
						(SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Rebate, Date, Hour) 
						VALUES (2, $idCampaing, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', $Rebate, '$Date', '$HourI')";
						echo $sql . "\n\n";
						$db->query($sql);
					}else{
						$sql = "UPDATE reports SET 
							Requests = Requests + $Requests, 
							Bids = Bids + $Bids, 
							Impressions = Impressions + $Impressions, 
							Revenue = Revenue + $Revenue, 
							VImpressions = VImpressions + $VImpressions,
							Clicks = Clicks + $Clicks,
							CompleteV = CompleteV + $CompleteV,
							Rebate = Rebate + $Rebate
							
						WHERE id = '$idStat' LIMIT 1";
					}
					//$db->query($sql);
					
				}
				//exit(0);
			}
		}
	}

//}
	$Date = date('Y-m-d', time() - 3600);
//updateReportCards($db3, $Date);
//	updateReportCards($db2, $Date);