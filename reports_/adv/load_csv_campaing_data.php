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
	$db3 = new SQL($advProd['host'], $advProd['db'], $advProd['user'], $advProd['pass']);
	
	require('/var/www/html/login/reports_/adv/common.php');
	require('/var/www/html/login/admin/lkqdimport/common.php');
	
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
	
	$JsonReport = json_decode(csvToJson('load_data.csv'));
	
	
	foreach($JsonReport as $InserData){
		$DealID = $InserData->DemandTagID;
		
		$sql = "SELECT id FROM campaign WHERE deal_id = $DealID LIMIT 1";
		$idCamp = $db3->getOne($sql);
		
		$sql = "SELECT * FROM campaign WHERE id = $idCamp LIMIT 1";
		$query = $db3->query($sql);
		if($db3->num_rows($query) > 0){
			$Camp = $db3->fetch_array($query);
				
			$CPM = $Camp['cpm'];
			$CPV = $Camp['cpv'];
			$CPC = $Camp['cpv'];
			$vCPM = $Camp['vcpm'];
			$RebatePercent = $Camp['rebate'];
		}else{
			die('Campaign data not found.');
		}
				
		$sql = "SELECT country_id FROM campaign_country WHERE campaign_id = $idCamp LIMIT 1";
		$idCountry = $db3->getOne($sql);
		
		$Requests = $InserData->TagRequests;
		$Bids = 0;
		$Impressions = $InserData->Impressions;
		$VImpressions = $InserData->ViewableImpressions;
		$Clicks = $InserData->Clicks;
		$CompleteV = $InserData->{'100Views'};
		$Complete25 = $InserData->{'25Views'};
		$Complete50 = $InserData->{'50Views'};
		$Complete75 = $InserData->{'75Views'};
		$CompleteVPerc = 0;
		
		if ($Impressions > 0 && $CPM > 0) {
			$Revenue = $Impressions * $CPM / 1000;
		} elseif ($CompleteV > 0 && $CPV > 0) {
			$Revenue = $CompleteV * $CPV;
		} elseif ($Clicks > 0 && $CPC > 0) {
			$Revenue = $Clicks * $CPC;
		} elseif ($VImpressions > 0 && $vCPM > 0) {
			$Revenue = $VImpressions * $vCPM / 1000;
		} else {
			$Revenue = 0;
		}
		
		if($RebatePercent > 0 && $Revenue > 0){
			$Rebate = $Revenue * $RebatePercent / 100;
		}else{
			$Rebate = 0;
		}
		
		$D = DateTime::createFromFormat('d/m/Y', $InserData->Time);
		$Date = $D->format('Y-m-d');
		$Hour = 23;
			
		echo $sql = "INSERT INTO reports
			(SSP, idCampaing, idCountry, Requests, Bids, Impressions, Revenue, VImpressions, Clicks, CompleteV, Complete25, Complete50, Complete75, CompleteVPer, Rebate, Date, Hour) 
			VALUES (4, $idCamp, $idCountry, '$Requests', '$Bids', '$Impressions', '$Revenue', '$VImpressions', '$Clicks', '$CompleteV', '$Complete25', '$Complete50', '$Complete75', '$CompleteVPerc', $Rebate, '$Date', '$Hour')";
		$db->query($sql);
		echo "\n";
		
	}
	