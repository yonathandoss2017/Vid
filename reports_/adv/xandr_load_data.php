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
	
	//$db2 = new SQL($advPre['host'], $advPre['db'], $advPre['user'], $advPre['pass']);
	$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	
	require('/var/www/html/login/reports_/adv/common.php');

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

function csvToJson($fname) {
    if (!($fp = fopen($fname, 'r'))) {
        die("Can't open file...");
    }
    $key = fgetcsv($fp,"1024",",");
    $json = array();
        while ($row = fgetcsv($fp,"1024",",")) {
        $json[] = array_combine($key, $row);
    }
    fclose($fp);
    
    return json_encode($json);
}


	$Date1 = date('Y-m-d', time() - 3600);
	//$Date1 = '2020-08-08';
	/*
	$date2 = new DateTime($Date1);
	$date2->modify('-1 day');
	$Date2 = $date2->format('Y-m-d');
	*/
	
	$cookie_file = '/var/www/html/login/reports_/adv/cookie_xandr';
	$csv_file = '/var/www/html/login/reports_/adv/xandr.csv';
	
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://api.appnexus.com/auth",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_COOKIEJAR => $cookie_file,
      CURLOPT_COOKIEFILE => $cookie_file,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => file_get_contents('/var/www/html/login/reports_/adv/auth'),
	  CURLOPT_HTTPHEADER => array(
	    "Content-Type: application/json"
	  ),
	));
	
	$Res = curl_exec($curl);
	curl_close($curl);
	
	print_r($Res);
	
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://api.appnexus.com/report",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_COOKIEJAR => $cookie_file,
      CURLOPT_COOKIEFILE => $cookie_file,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => file_get_contents('/var/www/html/login/reports_/adv/network_analytics'),
	  CURLOPT_HTTPHEADER => array(
	    "Content-Type: application/json"
	  ),
	));
	
	$Json = curl_exec($curl);
	curl_close($curl);
	$Decoded = json_decode($Json);
	
	print_r($Decoded);

	$ReportID = $Decoded->response->report_id;
	echo "REPORTID: $ReportID \n\n";
	
	$Status = 'pending';
	
	while($Status == 'pending'){
		sleep(3);
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.appnexus.com/report?id=$ReportID",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_COOKIEJAR => $cookie_file,
	      CURLOPT_COOKIEFILE => $cookie_file,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		));
		
		$Json = curl_exec($curl);
		curl_close($curl);
		$Decoded = json_decode($Json);
		
		$Status = $Decoded->response->execution_status;
		$URL = $Decoded->response->report->url;
		
		echo "$Status - $URL \n";
	}
	
	if($Status == 'ready'){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.appnexus.com/$URL",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_COOKIEJAR => $cookie_file,
	      CURLOPT_COOKIEFILE => $cookie_file,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		));
		
		$ReportCSV = curl_exec($curl);
		curl_close($curl);
		
		file_put_contents($csv_file, $ReportCSV);
	}else{
		exit('Error');
	}
	
	$JsonReport = csvToJson($csv_file);
	
	$ReportResults = json_decode($JsonReport);
	
//	print_r($ReportResults);
//	exit(0);
	
	$ActiveDeals = array();
	$CampaingData = array();
	$sql = "SELECT * FROM campaign WHERE ssp_id = 6 AND status = 1";
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		while($Camp = $db3->fetch_array($query)){
			$idCamp = $Camp['id'];
			
			$ActiveDeals[$idCamp] = $Camp['deal_id'];
			
			$CampaingData[$idCamp]['DealId'] = $Camp['deal_id'];
			$CampaingData[$idCamp]['Rebate'] = $Camp['rebate'];
			
			$countryId = 999;
			$sql = "SELECT COUNT(*) FROM campaign_country WHERE campaign_id = '$idCamp' ";
			if($db3->getOne($sql) == 1){
				$sql = "SELECT country_id FROM campaign_country WHERE campaign_id = '$idCamp' ";
				$countryId = $db3->getOne($sql);
			}
			
			$CampaingData[$idCamp]['Country'] = $countryId;
			
			//FIXED RANGES
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
	
	foreach($ReportResults as $Row){
		//if($Deal->deal_id == 'VID-CAD-00003'){	
		if(in_array($Row->deal_id, $ActiveDeals)){
			$idCampaing = array_search($Row->deal_id, $ActiveDeals);
			$RebatePercent = $CampaingData[$idCampaing]['Rebate'];
			$DealID = $CampaingData[$idCampaing]['DealId'];
			$idCountry = $CampaingData[$idCampaing]['Country'];
			
			$CVTR = $CampaingData[$idCampaing]['CVTR'];
			$CCTR = $CampaingData[$idCampaing]['CCTR'];
			$CView = $CampaingData[$idCampaing]['CView'];


			$DateH = new DateTime($Row->hour);
			$Hour = $DateH->format('H');
			$Date = $DateH->format('Y-m-d');
			
			$Requests = $Row->ad_requests;
			$Bids = 0;//$Row->bids_done;
			$Revenue = $Row->revenue;
			$Impressions = $Row->imps;
			
			$Clicks = $Row->clicks;
			$CompleteV = $Row->completions;

			$Complete25 = $Row->{'25_pcts'};
			$Complete50 = $Row->{'50_pcts'};
			$Complete75 = $Row->{'75_pcts'};
									
			$VImpressions = 0;
			
			if($RebatePercent > 0 && $Revenue > 0){
				$Rebate = $Revenue * $RebatePercent / 100;
			}else{
				$Rebate = 0;
			}
			
			$sql = "SELECT id FROM reports WHERE SSP = 6 AND idCampaing = $idCampaing AND Date = '$Date' AND Hour = '$Hour'";
			$idRepRow = $db->getOne($sql);
			
			if($idRepRow > 0){
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
					Rebate = $Rebate
					WHERE id = $idRepRow LIMIT 1";
			}else{		
				$sql = "INSERT INTO reports
				(SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, Rebate, Date, Hour) 
				VALUES (6, $idCampaing, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', $Complete75, '$Rebate', '$Date', '$Hour')";
			}
			echo $sql . "\n\n";
			$db->query($sql);
		}
	}
	
	$Date = date('Y-m-d', time() - 3600);
	updateReportCards($db3, $Date);
	//updateReportCards($db2, $Date);