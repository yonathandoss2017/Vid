<?php	
	//exit();
	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	require('/var/www/html/login/admin/appnxsimport/common.php');
	
	//exit(0);
	
	$cookie_file = '/var/www/html/login/admin/appnxsimport/cookie.txt';
	
	//$ImportData = getHourDataCSV($DateFrom, $DateTo, $HFrom, $HTo);
	
	
	
	logIn();
	
	$headers2 = array(
		"accept" => "application/json",
		"accept-encoding" => "gzip, deflate, br",
		"accept-language" => "en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6",
		"cache-control" => "no-cache",
		"pragma" => "no-cache",
		"referer" => "https://console.appnexus.com/dmp/segments",
		"sec-fetch-dest" => "empty",
		"sec-fetch-mode" => "cors",
		"sec-fetch-site" => "same-origin",
		"user-agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36"
	);

	$url2 = 'https://console.appnexus.com/dmp/api/current-data-providers?num_elements=100';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url2);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers2);
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
	$result = curl_exec($ch);
	curl_close($ch); 
	
	$arRes2 = json_decode($result);
	
	//echo '"Name","Data Provider","Description"' . "\n";
	$Data = '';
	$Providers = array();

	foreach($arRes2->response->{"current-data-providers"} as $K => $R){
		$Providers[$R->member_id] = $R->node_name;
	}
	
	for($P = 0; $P <= 414; $P++){
	//	for($P = 0; $P <= 0; $P++){
		
		echo "Page: " . $P . "\n\n";
		
		$Offset = $P * 500;
		
		$headers1 = array(
			"accept" => "application/json",
			"accept-encoding" => "gzip, deflate, br",
			"accept-language" => "en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6",
			"cache-control" => "no-cache",
			"pragma" => "no-cache",
			"referer" => "https://console.appnexus.com/dmp/segments",
			"sec-fetch-dest" => "empty",
			"sec-fetch-mode" => "cors",
			"sec-fetch-site" => "same-origin",
			"user-agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36"
		);
		
		$url = 'https://console.appnexus.com/dmp/api/data-segment?start_element=' . $Offset . '&num_elements=500&include_shared=true&sort=id.asc&version=2&only_providers=false';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
		//curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
	    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	    curl_setopt($ch, CURLOPT_VERBOSE, false);
		$result = curl_exec($ch);
		curl_close($ch); 
		
		$arRes = json_decode($result);
		//echo $result;
		
		$Data = '';

		foreach($arRes->response->{"data-segments"} as $Key => $Row){
		/*
			$Row['short_name']
			$Row['description']
            $Row['category']
		*/	
		
			
			$Name = $Row->short_name;
			$DP = $Providers[$Row->member_id];
		
			
			$Data .=  '"' . $Name . '","' . $DP . '","' . $Row->description . '"' . "\n";
			
			echo '"' . $Name . '","' . $DP . '"' . "\n";
			
		}
		
		//$OldData = file_get_contents('/var/www/html/login/admin/appnxsimport/import2.csv');
		file_put_contents('/var/www/html/login/admin/appnxsimport/import2.csv', $Data, FILE_APPEND);
		
	}
	
	
	exit(0);	
	if($ImportData === false){
		echo "Loggin in... \n\n";
		
		$ImportData = getHourDataCSV($DateFrom, $DateTo, $HFrom, $HTo);
	}