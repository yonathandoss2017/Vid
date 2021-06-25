<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/reports_/adv/config.php');
	require('/var/www/html/login/db.php');
	$db = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	
	$dbuser3 = "root";
	$dbpass3 = "pthFTa8Lp25xs7Frkqgkz5HRebmwVGPY";
	$dbhost3 = "aa14extn6ty9ilx.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname3 = "vidoomy-advertisers-panel";
	$db3 = new SQL($dbhost3, $dbname3, $dbuser3, $dbpass3);
	
	/*
		PRE-PRODUCCION
		$dbuser3 = "root";
		$dbpass3 = "Kw6tbHnTtukP3tV2pDqBs7xP6TG2DhFe";
		$dbhost3 = "aazw79txt1iy6x.cme5dsqa4tew.us-east-2.rds.amazonaws.com";
		$dbname3 = "vidoomy-advertisers-panel";
		$db3 = new SQL($dbhost3, $dbname3, $dbuser3, $dbpass3);
	*/

	require('/var/www/html/login/reports_/adv/common.php');
	
	$sql = "SELECT * FROM campaign WHERE create_from = 'DEAL_FORM' AND status = 4 AND ssp_id = 1 LIMIT 1";
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		$Camp = $db3->fetch_array($query);
		$idCamp = $Camp['id'];
		$DSPID = $Camp['buyer_id'];
		//print_r($Camp);

		if($Camp['deal_id'] == ''){
			
			if($Camp['start_at'] != ''){
				$dateStart = new DateTime($Camp['start_at']);
				$StartAt = $dateStart->format('Ymd');
			}else{
				$StartAt = date('Ymd');
			}
			
			if($Camp['end_at'] != ''){
				$dateEnd = new DateTime($Camp['end_at']);
				$EndAt = $dateEnd->format('Ymd');
			}else{
				$EndAt = '';
			}
			
			if($Camp['device'] == 2){
				$ArDevice = array("7439313"); //MOBILE
			}elseif($Camp['device'] == 3){
				$ArDevice = array("7585793"); //DESKTOP
			}else{
				$ArDevice = array(
					"7585793", //DESKTOP
					"7439313" //MOBILE
				);
			}
			
			
			$CountryIso = array();
			$sql = "SELECT * FROM campaign_country WHERE campaign_id = $idCamp";
			$query2 = $db3->query($sql);
			if($db3->num_rows($query2) > 0){
				while($Country = $db3->fetch_array($query2)){
					$idCountry = $Country['country_id'];
					$sql = "SELECT iso FROM country WHERE id = $idCountry LIMIT 1";
					$CountryIso[] = $db->getOne($sql);
				}
				
				$ActivationCondition = array(
					"operator"=>"and",
					"left" => array(
						"key" => "Zone",
						"operator" => "=~",
						"value" => $ArDevice
					),
					"right" => array(
						"operator" => "=~",
						"key" => "Geo_CountryId",
						"value" => $CountryIso
					)
				);
			}else{
				$ActivationCondition = array(
					"operator"=>"and",
					"left" => array(
						"key" => "Zone",
						"operator" => "=~",
						"value" => $ArDevice
					)
				);
			}
			
			$BuyersJson = json_decode(file_get_contents('https://sfx.freewheel.tv/api/inbound/buyer?token=1235953da965bb433a84a81e5fd6e482cc6a2e3b'));
			
			$idBuyer = 0;
			
			foreach($BuyersJson->results as $ActiveBIds){
				//echo $DSPID;
				if($ActiveBIds->{'seat-id'} == $DSPID){
					$idBuyer = $ActiveBIds->id;
				}
			}
			
			if($idBuyer == 0){

				$sql = "UPDATE campaign SET status = 5, deal_id = 'ERROR' WHERE id = '$idCamp' LIMIT 1";
				$db3->query($sql);
				
				$When = date('Y-m-d H:m:s');
				$Log = "|$When: 'Buyer ID not found ($DSPID)' \n";
				
				file_put_contents('/var/www/html/login/deals/log/newdeal_error.txt', $Log, FILE_APPEND);
				
				exit();
			}
			
			if($EndAt != ''){
				$DealData = array(
					"deal" => array(
						"name" => $Camp['name'],
						//"public-id" => '',
						"start-date" => $StartAt,
						"end-date" => $EndAt,
						"pricing-type" => "FIXED_PRICE",
						"fixed-price-cpm" => number_format($Camp['cpm'], 2, '.', ','),
						//"floor-price-policy" => "none",
						"show-domain" => "SHOW",
						"bid-priority" => "same-as-buyer",
						"activation-condition" => $ActivationCondition,
						"target-buyers" => array($idBuyer) //1638289
					)
				);
			}else{
				$DealData = array(
					"deal" => array(
						"name" => $Camp['name'],
						//"public-id" => '',
						"start-date" => $StartAt,
						"pricing-type" => "FIXED_PRICE",
						"fixed-price-cpm" => number_format($Camp['cpm'], 2, '.', ','),
						//"floor-price-policy" => "none",
						"show-domain" => "SHOW",
						"bid-priority" => "same-as-buyer",
						"activation-condition" => $ActivationCondition,
						"target-buyers" => array($idBuyer) //1638289
					)
				);
			}
			
			//print_r($DealData);
				
			$SentData = json_encode($DealData);
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://sfx.freewheel.tv/api/inbound/deal/?token=1235953da965bb433a84a81e5fd6e482cc6a2e3b" );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $SentData); 
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain')); 
			
			$result = curl_exec ($ch);
	
			//$result = '{"results":{"id":917233},"execTime":0.061323881149291992}';
			
			$DecodedResult = json_decode($result);
			
			$When = date('Y-m-d H:m:s');
			
			if(property_exists($DecodedResult, 'results')){
				$NewDealId = $DecodedResult->results->id;
				$Log = "|NEW FW ID: $idCamp - $When: $result  \nDATA SENT: $SentData\n";
				
				file_put_contents('/var/www/html/login/deals/log/newdeal.txt', $Log, FILE_APPEND);
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://sfx.freewheel.tv/api/inbound/deal/$NewDealId?token=1235953da965bb433a84a81e5fd6e482cc6a2e3b" );
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain')); 
				
				$result = curl_exec ($ch);
				$DecodedResult = json_decode($result);
				
				$DealId = $DecodedResult->results->{'public-id'};
				
				$sql = "UPDATE campaign SET status = 1, deal_id = '$DealId' WHERE id = '$idCamp' LIMIT 1";
				$db3->query($sql);
			}else{
				$sql = "UPDATE campaign SET status = 5, deal_id = 'ERROR' WHERE id = '$idCamp' LIMIT 1";
				$db3->query($sql);
				
				$Log = "|NEW FW ID: $idCamp - $When: $result \nDATA SENT: $SentData\n";
				
				file_put_contents('/var/www/html/login/deals/log/newdeal_error.txt', $Log, FILE_APPEND);
			}
		}		
	}
	
	
	$DateU = date('Y-m-d H:i:s', time() - (4 * 3600) - 600);
	
	$sql = "SELECT * FROM campaign WHERE create_from = 'DEAL_FORM' AND status = 5 AND ssp_id = 1 AND modified_at >= '$DateU' LIMIT 1";
	//$sql = "SELECT * FROM campaign WHERE id = 1053 LIMIT 1";
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		$Camp = $db3->fetch_array($query);
		$idCamp = $Camp['id'];
		$DSPID = $Camp['buyer_id'];
		$DealId = $Camp['deal_id'];
		echo $DealId . "\n";
		//print_r($Camp);

		if($DealId != ''){
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://sfx.freewheel.tv/api/inbound/deal?token=1235953da965bb433a84a81e5fd6e482cc6a2e3b" );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain')); 
			
			$result = curl_exec ($ch);
			
			$DealsList = json_decode($result);
			
			$idFW = 0;
			
			foreach($DealsList->results as $Deal){
				//echo $Deal->{'public-id'} . "\n";
				if($Deal->{'public-id'} == $DealId){
					$idFW = $Deal->id;
					echo "<-$idFW->\n";
				}
			}
			
			if($idFW > 0){
			
				if($Camp['start_at'] != ''){
					$dateStart = new DateTime($Camp['start_at']);
					$StartAt = $dateStart->format('Ymd');
				}else{
					$StartAt = date('Ymd');
				}
				
				if($Camp['end_at'] != ''){
					$dateEnd = new DateTime($Camp['end_at']);
					$EndAt = $dateEnd->format('Ymd');
				}else{
					$EndAt = '';
				}
				
				if($Camp['device'] == 2){
					$ArDevice = array("7439313"); //MOBILE
				}elseif($Camp['device'] == 3){
					$ArDevice = array("7585793"); //DESKTOP
				}else{
					$ArDevice = array(
						"7585793", //DESKTOP
						"7439313" //MOBILE
					);
				}
				
				$CountryIso = array();
				$sql = "SELECT * FROM campaign_country WHERE campaign_id = $idCamp";
				$query2 = $db3->query($sql);
				if($db3->num_rows($query2) > 0){
					while($Country = $db3->fetch_array($query2)){
						$idCountry = $Country['country_id'];
						$sql = "SELECT iso FROM country WHERE id = $idCountry LIMIT 1";
						$CountryIso[] = $db->getOne($sql);
					}
					
					$ActivationCondition = array(
						"operator"=>"and",
						"left" => array(
							"key" => "Zone",
							"operator" => "=~",
							"value" => $ArDevice
						),
						"right" => array(
							"operator" => "=~",
							"key" => "Geo_CountryId",
							"value" => $CountryIso
						)
					);
				}else{
					$ActivationCondition = array(
						"operator"=>"and",
						"left" => array(
							"key" => "Zone",
							"operator" => "=~",
							"value" => $ArDevice
						)
					);
				}
				
				if($EndAt != ''){
					$DealData = array(
						"deal" => array(
							"name" => $Camp['name'],
							//"public-id" => '',
							"start-date" => $StartAt,
							"end-date" => $EndAt,
							"pricing-type" => "FIXED_PRICE",
							"fixed-price-cpm" => $Camp['cpm'],
							//"floor-price-policy" => "none",
							"show-domain" => "SHOW",
							"bid-priority" => "same-as-buyer",
							"activation-condition" => $ActivationCondition,
							//"target-buyers" => array(1638289) //$idBuyer
						)
					);
				}else{
					$DealData = array(
						"deal" => array(
							"name" => $Camp['name'],
							//"public-id" => '',
							"start-date" => $StartAt,
							"pricing-type" => "FIXED_PRICE",
							"fixed-price-cpm" => $Camp['cpm'],
							//"floor-price-policy" => "none",
							"show-domain" => "SHOW",
							"bid-priority" => "same-as-buyer",
							"activation-condition" => $ActivationCondition,
							//"target-buyers" => array(1638289) //$idBuyer
						)
					);
				}
				
				$SentData = json_encode($DealData);
	
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://sfx.freewheel.tv/api/inbound/deal/$idFW?token=1235953da965bb433a84a81e5fd6e482cc6a2e3b" );
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($ch, CURLOPT_POSTFIELDS, $SentData);
				
				curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
				curl_setopt($ch, CURLOPT_TIMEOUT, 0);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				
				curl_setopt($ch, CURLOPT_VERBOSE, true);
				
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain')); 
				
				$result = curl_exec ($ch);
							
				$DecodedResult = json_decode($result);
				
				$When = date('Y-m-d H:m:s');
				
				if(property_exists($DecodedResult, 'results')){
					$NewDealId = $DecodedResult->results->id;
					$Log = "|UPDATE FW ID: $idCamp - $When: $result \n";
					
					file_put_contents('/var/www/html/login/deals/log/newdeal.txt', $Log, FILE_APPEND);
									
					$sql = "UPDATE campaign SET status = 1, modified_at = '$DateU' WHERE id = '$idCamp' LIMIT 1";
					$db3->query($sql);
				}else{
					$sql = "UPDATE campaign SET status = 5 WHERE id = '$idCamp' LIMIT 1";
					$db3->query($sql);
					
					$Log = "|UPDATE FW ID: $idCamp - $When: $result \nDATA SENT: $SentData\n";
					
					file_put_contents('/var/www/html/login/deals/log/newdeal_error.txt', $Log, FILE_APPEND);
				}
			}else{
				$When = date('Y-m-d H:m:s');
				
				$Log = "|UPDATE FW ID: $idCamp - $When: Deal ID not found on FW Deal list. $DealId <=> $idFW \n";
					
				file_put_contents('/var/www/html/login/deals/log/newdeal_error.txt', $Log, FILE_APPEND);
			}
		}else{
			//UPDATE
		}		
	}