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
	require('/var/www/html/login/deals/spotx_api.php');

	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	$db3 = new SQL($advProd["host"], $advProd["db"], $advProd["user"], $advProd["pass"]);
	
	/*
		PRE-PRODUCCION
		$db3 = new SQL($advPre["host"], $advPre["db"], $advPre["user"], $advPre["pass"]);
	*/

	require('/var/www/html/login/reports_/adv/common.php');
	
	$AdjustTime =  time() + 240;
	
	$sql = "SELECT * FROM campaign WHERE create_from = 'DEAL_FORM' AND status = 4 AND ssp_id = 2 LIMIT 1";
	//$sql = "SELECT * FROM campaign WHERE id = 1042 LIMIT 1";
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		$Camp = $db3->fetch_array($query);
		$idCamp = $Camp['id'];
		$DSPID = $Camp['buyer_id'];
		$SpxDSPId = $Camp['spotx_dsp_id'];
		
		$sql = "SELECT advertiser_id FROM dsps_spotx WHERE id = $SpxDSPId LIMIT 1";
		$SpxDSP = $db3->getOne($sql);
		//print_r($Camp);

		if($Camp['deal_id'] == ''){// OR $Camp['deal_id'] == 'ERROR'
		
			$Token = getSpotXToken();
			
			if($Camp['start_at'] != ''){
				$dateStart = new DateTime($Camp['start_at']);
				$StartAt = $dateStart->format('Y-m-d');
				
				if($StartAt == date('Y-m-d', $AdjustTime)){
					$StartAtHour = date('g:i A', $AdjustTime);
				}else{
					$StartAtHour = '01:01 AM';
				}
				
			}else{
				$StartAt = date('Y-m-d');
				$StartAtHour = date('g:i A', $AdjustTime);
			}
			
			if($Camp['end_at'] != ''){
				$dateEnd = new DateTime($Camp['end_at']);
				$EndAt = $dateEnd->format('Y-m-d');
				$EndAtHour = '11:59 PM';
			}else{
				$EndAt = '';
				$EndAtHour = '';
			}
			
			$Targeting = array();
			
			//DESKTOP
			if($Camp['device'] == 3){
				$deviceTargeting = new stdClass();
				
				$deviceTargeting->category_id = 10;
				$deviceTargeting->operator = "is any of";
				$deviceTargeting->options = array(2);
				
				$Targeting[] = $deviceTargeting;
			}
			
			//MOBILE
			if($Camp['device'] == 2){
				$deviceTargeting = new stdClass();
				
				$deviceTargeting->category_id = 10;
				$deviceTargeting->operator = "is any of";
				$deviceTargeting->options = array(1,4,5);
				
				$Targeting[] = $deviceTargeting;
			}
			
			$CountriesIds = array();
			$sql = "SELECT * FROM campaign_country WHERE campaign_id = $idCamp";
			$query2 = $db3->query($sql);
			if($db3->num_rows($query2) > 0){
				while($Country = $db3->fetch_array($query2)){
					$idCountry = $Country['country_id'];
					$sql = "SELECT nice_name FROM country WHERE id = $idCountry LIMIT 1";
					$NiceName = $db->getOne($sql);
					$CountriesIds[] = getCountrySpotXID($NiceName, $Token);
				}
			}
			
			if(count($CountriesIds) > 0){
				
				$countryTargeting = new stdClass();
				$countryTargeting->category_id = 43;
				$countryTargeting->operator = "is any of";
				
				$countryTargeting->options = $CountriesIds;
				
				$Targeting[] = $countryTargeting;
			}
			
			$DealData = array(
			    "name" => $Camp['name'],
			    "status" => "Active",
			    "paused" => false,
			    "priority" => 1,
			    "disable_ad_blocking" => true,
			    "reporting_timezone" => "+0000",
			    "honor_channel_price_floor" => true,
			    "price_floor_currency" => "USD",
			    "source" => "Programmatic Direct",
			    "fixed_cpm" => $Camp['cpm'],
			    "fixed_cpm_type" => "Fixed",
			    "dsp_partner_id" => $SpxDSP,
			    "ad_review_enabled" => true,
			    "start_datetime" => array(
			        "date" => $StartAt,
			        "time" => $StartAtHour,
			        "timezone" => "+0000"
			    ),
			    "end_datetime" => array(
			        "date" => $EndAt,
			        "time" => $EndAtHour,
			        "timezone" => "+0000"
			    ),
			    "targeting_options" => $Targeting
			);
			
			
			
			if($EndAt == ''){
				unset($DealData['end_datetime']);
				//echo "END AT <$EndAt> \n";
			}
			
			$JsonData = json_encode($DealData);
			
			$curl2 = curl_init();
			curl_setopt_array($curl2, array(
			  CURLOPT_URL => "https://api.spotxchange.com/1.1/Publisher(218443)/Campaign",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_POSTFIELDS => $JsonData,
			  CURLOPT_VERBOSE => false,
			  CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
			    "Authorization: Bearer $Token"
			  ),
			));
			
			$Json = curl_exec($curl2);
			curl_close($curl2);
						
			$DecodedResult = json_decode($Json);
			
			$When = date('Y-m-d H:m:s');
			
			if(property_exists($DecodedResult, 'error')){
				$sql = "UPDATE campaign SET status = 5, deal_id = 'ERROR' WHERE id = '$idCamp' LIMIT 1";
				$db3->query($sql);
				
				$Log = "|NEW SPX $When: $Json \n";
				
				file_put_contents('/var/www/html/login/deals/log/newdeal_error.txt', $Log, FILE_APPEND);
			}else{
				$DealId = $DecodedResult->id;
				$Log = "|NEW SPX $When: $Json \n";
				
				file_put_contents('/var/www/html/login/deals/log/newdeal.txt', $Log, FILE_APPEND);
								
				$sql = "UPDATE campaign SET status = 1, deal_id = '$DealId' WHERE id = '$idCamp' LIMIT 1";
				$db3->query($sql);
			}
		}	
	}
	
	
	//GET ALL SPOTX DEALS UPDATED ON THE LAST 10 MINUTES
	$DateU = date('Y-m-d H:i:s', time() - (4 * 3600) - 600);
	
	$sql = "SELECT * FROM campaign WHERE create_from = 'DEAL_FORM' AND (status = 1 OR status = 2) AND ssp_id = 2 AND modified_at >= '$DateU' LIMIT 1";
	//$sql = "SELECT * FROM campaign WHERE id = 373 LIMIT 1";
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		$Camp = $db3->fetch_array($query);
		$idCamp = $Camp['id'];
		$DSPID = $Camp['buyer_id'];
		$DealId = $Camp['deal_id'];
		$Status = $Camp['status'];
		
		if($DealId != ''){
			
			//GET API TOKEN
			$Token = getSpotXToken();
			
			//GET CURRENT DEAL CONFIGURATION
			$curl2 = curl_init();
			curl_setopt_array($curl2, array(
			  CURLOPT_URL => "https://api.spotxchange.com/1.1/Publisher(218443)/Campaign($DealId)",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_VERBOSE => false,
			  CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
			    "Authorization: Bearer $Token"
			  ),
			));
			
			$Json = curl_exec($curl2);
			curl_close($curl2);
						
			$CurrentCampaignData = json_decode($Json);
			
			//UPDATE THE CONFIGURATION
			unset($CurrentCampaignData->start_datetime);
			$CurrentCampaignData->name = $Camp['name'];
			$CurrentCampaignData->fixed_cpm = $Camp['cpm'];
			
			//RE-GENERATE TARGETING OPTIONS
			unset($CurrentCampaignData->targeting_options);
			
			$Targeting = array();
			
			//IF DESKTOP
			if($Camp['device'] == 3){
				$deviceTargeting = new stdClass();
				
				$deviceTargeting->category_id = 10;
				$deviceTargeting->operator = "is any of";
				$deviceTargeting->options = array(2);
				
				$Targeting[] = $deviceTargeting;
			}
			
			//IF MOBILE
			if($Camp['device'] == 2){
				$deviceTargeting = new stdClass();
				
				$deviceTargeting->category_id = 10;
				$deviceTargeting->operator = "is any of";
				$deviceTargeting->options = array(1,4,5);
				
				$Targeting[] = $deviceTargeting;
			}
			
			//GENERATE COUNTRY TARGETING
			$CountriesIds = array();
			$sql = "SELECT * FROM campaign_country WHERE campaign_id = $idCamp";
			$query2 = $db3->query($sql);
			if($db3->num_rows($query2) > 0){
				while($Country = $db3->fetch_array($query2)){
					$idCountry = $Country['country_id'];
					$sql = "SELECT nice_name FROM country WHERE id = $idCountry LIMIT 1";
					$NiceName = $db->getOne($sql);
					$CountriesIds[] = getCountrySpotXID($NiceName, $Token);
				}
			}
			
			if(count($CountriesIds) > 0){
				
				$countryTargeting = new stdClass();
				$countryTargeting->category_id = 43;
				$countryTargeting->operator = "is any of";
				
				$countryTargeting->options = $CountriesIds;
				
				$Targeting[] = $countryTargeting;
			}
			
			if(count($Targeting) > 0){
				$CurrentCampaignData->targeting_options = $Targeting;
			}
			
			if($Status == 2){
				$CurrentCampaignData->paused = true;
			}else{
				$CurrentCampaignData->paused = false;
			}
			
			$CurrentCampaignData->ad_review_enabled = true;
			
			//JSON THE CAMPAING OBJECT AND SEND			
			$JsonData = json_encode($CurrentCampaignData);
			
			$curl2 = curl_init();
			curl_setopt_array($curl2, array(
			  CURLOPT_URL => "https://api.spotxchange.com/1.1/Publisher(218443)/Campaign($DealId)",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "PUT",
			  CURLOPT_POSTFIELDS => $JsonData,
			  CURLOPT_VERBOSE => false,
			  CURLOPT_HTTPHEADER => array(
				"Content-Type: application/json",
			    "Authorization: Bearer $Token"
			  ),
			));
			
			$Json = curl_exec($curl2);
			curl_close($curl2);
						
			$DecodedResult = json_decode($Json);
			
			//print_r($DecodedResult);
			
			//UPDATE AND LOG THE RESULTS
			
			$When = date('Y-m-d H:m:s');
			
			if(property_exists($DecodedResult, 'error')){
				$sql = "UPDATE campaign SET status = 5 WHERE id = '$idCamp' LIMIT 1";
				$db3->query($sql);
				
				$Log = "|UPDATE SPX $When: $Json \n";
				
				file_put_contents('/var/www/html/login/deals/log/newdeal_error.txt', $Log, FILE_APPEND);
			}else{
				$DealId = $DecodedResult->id;
				$Log = "|UPDATE SPX $When: $Json \n";
				
				file_put_contents('/var/www/html/login/deals/log/newdeal.txt', $Log, FILE_APPEND);
								
				$sql = "UPDATE campaign SET status = 1, modified_at = '$DateU' WHERE id = '$idCamp' LIMIT 1";
				$db3->query($sql);
			}
			
		}		
	}