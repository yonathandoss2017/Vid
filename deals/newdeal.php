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
	//$dbhost3 = "aa4mgb1tsk2y6v.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbhost3 = "aa14extn6ty9ilx.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname3 = "vidoomy-advertisers-panel";
	$db3 = new SQL($dbhost3, $dbname3, $dbuser3, $dbpass3);
	
	require('/var/www/html/login/reports_/adv/common.php');
	
	
	
	
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://sfx.freewheel.tv/api/inbound/deal?token=1235953da965bb433a84a81e5fd6e482cc6a2e3b" );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain')); 
	
	$result = curl_exec ($ch);
	
	$DealsList = json_decode($result);
	
	$idFW = 0;
	
	foreach($DealsList->results as $Deal){
		echo $Deal->{'public-id'} . "\n";
		$idFW = $Deal->id;
	}
	
	//echo $result;
	
	
	
	exit(0);
	/*
	
	$DealData = array(
		"deal" => array(
			"name" => "TEST deal - Test 2",
			"public-id" => "TEST-DL-0004",
			"start-date" => "20200819",
			"fixed-price-cpm" => '4.22',
			"floor-price-policy" => "none",
			"show-domain" => "SHOW",
			"bid-priority" => "same-as-buyer",
			"activation-condition" => array(
				"key" => "Geo_CountryId", 
				"operator" => "=~", 
				"value" => array("AR")
			),
			"target-buyers" => array(1638289)
		)
	);
	
	$SentData = json_encode($DealData);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://sfx.freewheel.tv/api/inbound/deal/?token=1235953da965bb433a84a81e5fd6e482cc6a2e3b" );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $SentData); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain')); 
	
	echo $result = curl_exec ($ch);
	
	//$result = '{"results":{"id":917233},"execTime":0.061323881149291992}';
	
	$DecodedResult = json_decode($result);
	
	$When = date('Y-m-d H:m:s');
	
	if(property_exists($DecodedResult, 'results')){
		$NewDealId = $DecodedResult->results->id;
		$Log = "|$When: $result \n";
		
		file_put_contents('/var/www/html/login/deals/log/newdeal.txt', $Log, FILE_APPEND);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://sfx.freewheel.tv/api/inbound/deal/$NewDealId?token=1235953da965bb433a84a81e5fd6e482cc6a2e3b" );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain')); 
		
		$result = curl_exec ($ch);
		$DecodedResult = json_decode($result);
		
		echo $DecodedResult->results->{'public-id'};
	}else{
		$Log = "|$When: $result \n";
		
		file_put_contents('/var/www/html/login/deals/log/newdeal_error.txt', $Log, FILE_APPEND);
	}
	*/
	
	
	
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
	
	$AdjustTime =  time() + 60;
	
	$Decoded = json_decode($Json);
	$Token = $Decoded->value->data->access_token;
	echo $Token;
	//exit();
	
	$DealData = array(
	    "name" => "Campaign_Test_Start_Time",
	    "status" => "Active",
	    "paused" => false,
	    "priority" => 1,
	    "disable_ad_blocking" => true,
	    "reporting_timezone" => "+0000",
	    "honor_channel_price_floor" => true,
	    "price_floor_currency" => "USD",
	    //"source" => "3rd Party Tag",
	    "source" => "Programmatic Direct",
	    "fixed_cpm" => 4.23,
	    "fixed_cpm_type" => "Fixed",
	    "dsp_partner_id" => 7025,//,
	    "start_datetime" => array(
	        "date" => date('Y-m-d', $AdjustTime),//"2020-09-16",
	        "time" => date('H:i A', $AdjustTime),//"12:01 AM",
	        "timezone" => "+0000"
	    )/*,
	    "end_datetime" => array(
	        "date" => "2020-09-20",
	        "time" => "12:01 AM",
	        "timezone" => "+0000"
	    )*/
	);
	
	//$DealData = json_decode(file_get_contents('json_spotx.json'));
	
	//echo "\n\n" . http_build_query('"false"') .  "\n\n";
	
	echo $JsonData = json_encode($DealData);
	
	echo  "\n\n\n\n\n\n";
	
	echo $SentData = http_build_query($DealData);
	
	echo  "\n\n\n\n\n\n";
//	echo $SentData = json_encode($DealData);
	//exit();
	//$SentData = file_get_contents('e.json');
	
	//echo $SentData;
	
	
/*    "preferred_slot": 0,
    "targeting_options": [
        {
            "category_id": 7,
            "operator": "is any of",
            "options": [
                "132210"
            ]
        },
        {
            "category_id": 23,
            "operator": "is any of",
            "options": [
                "1",
                "3"
            ]
        }
    ],
*/
    
	$curl2 = curl_init();
	curl_setopt_array($curl2, array(
	  CURLOPT_URL => "https://api.spotxchange.com/1.1/Publisher(218443)/Campaign",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => $JsonData,//"{\"name\":\"CampaignTestVidoomy90\",\"status\":\"Active\",\"paused\":false,\"priority\":1,\"disable_ad_blocking\":true,\"reporting_timezone\":\"+0000\",\"honor_channel_price_floor\":true,\"price_floor_currency\":\"USD\",\"source\":\"Programmatic Direct\",\"fixed_cpm\":4.23,\"fixed_cpm_type\":\"Fixed\",\"dsp_partner_id\":7025,\"start_datetime\":{\"date\":\"2020-09-16\",\"time\":\"12:01 AM\",\"timezone\":\"+0000\"},\"end_datetime\":{\"date\":\"2020-09-20\",\"time\":\"12:01 AM\",\"timezone\":\"+0000\"}}",
	  CURLOPT_VERBOSE => false,
	  CURLOPT_HTTPHEADER => array(
		"Content-Type: application/json",
	    "Authorization: Bearer $Token"
	  ),
	));
	
	$Json = curl_exec($curl2);
	curl_close($curl2);
	
	echo $Json;

	/*
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://api.spotxchange.com/1.1/Publisher(218443)/Campaign");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $DealData); 
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $Token"));
	echo $result = curl_exec ($ch);

	
	*.
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//{"key":"Zone","operator":"=~","value":["7585793"]}
	
	/*
	
	$DealData = array(
		"deal" => array(
			"name" => "TEST deal - Fede 2",
			"activation-condition" => array(
				"operator"=>"and",
				"left" => array(
					"key" => "Zone",
					"operator" => "=~",
					"value" => array(
						"7585793", //DESKTOP
						"7439313" //MOBILE
					)
				),
				"right" => array(
					"operator" => "=~",
					"key" => "Geo_CountryId",
					"value" => "ES"
				)
			)
		)
	);
	
	$SentData = json_encode($DealData);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://sfx.freewheel.tv/api/inbound/deal/917233?token=1235953da965bb433a84a81e5fd6e482cc6a2e3b" );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $SentData);
	
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain')); 
	
	$result = curl_exec ($ch);
	
	echo $result;
	exit(0);
	
	
	
	
	
	
	
	
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://sfx.freewheel.tv/api/inbound/deal/917233?token=1235953da965bb433a84a81e5fd6e482cc6a2e3b" );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/plain')); 
	
	$result = curl_exec ($ch);
	
	echo $result;
	
	
	*/