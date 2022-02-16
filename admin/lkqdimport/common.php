<?php

define('TAG_TYPE_VIDEO', 1);
define('TAG_TYPE_VAST', 2);
define('UNAUTHORIZED_PREFIX', 'unauthorized_from_LKQD');
	
	$ExtraP[0] = 27;
	$ExtraP[1] = 28;
	$ExtraP[2] = 29;
	$ExtraP[3] = 30;
	$ExtraP[4] = 31;
	$ExtraP[5] = 32;
	$ExtraP[6] = 33;
	$ExtraP[7] = 30;
	$ExtraP[8] = 27;
	$ExtraP[9] = 33;
	
function logIn($Source = 'Unknown'){
	global $sessionId, $cookie_file, $lkqdCred;

	file_put_contents($cookie_file, "");
	
	$headers1 = array(
		'Accept: application/json, text/plain, */*',
		'LKQD-Api-Version: 88',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$url = 'https://ui-api.lkqd.com/time';
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
	
	//echo $result;
	//exit(0);
	
	
	$headers2 = array(
		'Access-Control-Request-Headers: content-type',
		'Access-Control-Request-Method: POST',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: no-cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);

	$url = 'https://api.lkqd.com/login';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers2);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	$result = curl_exec($ch);
	//$information = curl_getinfo($ch);
	curl_close($ch); 
	
	//echo $result;
	
	$json_encode = json_encode($lkqdCred);
	$length = strlen($json_encode);
	
	$headers3 = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		//'LKQD-Api-Version: 88',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$url = 'https://api.lkqd.com/login';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers3);
	curl_setopt($ch, CURLOPT_POST, 1);
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$json_encode);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	$result = curl_exec($ch);
	$information = curl_getinfo($ch);
	curl_close($ch); 
	
	file_put_contents('/var/www/html/login/admin/lkqdimport/login_log.txt', date('Y-m-d H:i:s') . " Source: $Source -> Information: " . print_r($information, true) . " \n Result: \n $result \n $cookie_file \n\n\n", FILE_APPEND);
	//echo $result;
}

function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

function getResultsHour($DateS, $Hi){
	global $sessionId, $cookie_file, $post;
	$headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'LKQD-Api-Version: 85',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/reports/7712',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36'
	);
	
	$post = array(
		"timeDimension" => "HOURLY",
		"reportType" => array("PARTNER", "SITE", "DOMAIN", "COUNTRY"),
		"metrics" => array("OPPORTUNITIES","IMPRESSIONS","CPM","REVENUE","COST","FORMAT_LOADS","CLICKS","FIRST_QUARTILES","MIDPOINTS","THIRD_QUARTILES","COMPLETED_VIEWS","AD_STARTS","VIEWABLE_IMPRESSIONS"),
		"reportFormat" => "JSON",
		"startDate" => $DateS,
		"startDateHour" => $Hi,
		"endDate" => $DateS,
		"endDateHour" => $Hi,
		"sort" => array(array(
			"field" => "FORMAT_LOADS",
			"order" => "desc"
			)),
		"timezone" => "America/New_York",
		//"limit" => 6000,
		//"offset" => $Offset,
		"whatRequest" => "breakdown"
	);
	
	$json = json_encode($post);
	$url = 'https://ui-api.lkqd.com/reports';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	$result = curl_exec($ch);
	curl_close($ch);  
	$deco = json_decode($result);
	
	return $deco;
}
function getResultsDay($DateS, $Offset = false){
	global $sessionId, $cookie_file, $post;
	$headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'LKQD-Api-Version: 88',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/reports/7712',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36'
	);
	
	$post = array(
		"timeDimension" => "DAILY",
		"reportType" => array("PARTNER", "SITE"),
		"metrics" => array("OPPORTUNITIES","IMPRESSIONS","CPM","REVENUE","COST","FORMAT_LOADS","CLICKS","FIRST_QUARTILES","MIDPOINTS","THIRD_QUARTILES","COMPLETED_VIEWS","AD_STARTS","VIEWABLE_IMPRESSIONS"),
		"reportFormat" => "JSON",
		"startDate" => $DateS,
		"endDate" => $DateS,
		"sort" => array(array(
			"field" => "FORMAT_LOADS",
			"order" => "desc"
			)),
		"timezone" => "America/New_York",
		//"limit" => 30000,
		//"offset" => $Offset,
		"whatRequest" => "breakdown"
	);
	
	$json = json_encode($post);
	$url = 'https://ui-api.lkqd.com/reports';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	$result = curl_exec($ch);
	curl_close($ch);  
	$deco = json_decode($result);
	
	return $deco;
}

function getDayDataCSV($DateFrom, $DateTo){
	global $sessionId, $cookie_file;
	$uuid = gen_uuid();
	$fileDownloadToken = rand(100000,999999);
	
	$post = array(
		"whatRequest" => "csv",
		"uuid" => $uuid,
		"reportFormat" => "CSV",
		"includeSummary" => "false",
		"dateRangeType" => "CUSTOM",
		"startDate" => $DateFrom,
		"endDate" => $DateTo,
		"startHour" => 0,
		"endHour" => 23,
		"timeDimension" => "DAILY",
		"timezone" => "America/New_York",
		"reportType" => array("PARTNER", "SITE", "DOMAIN", "COUNTRY"),
		"environmentIds" => array(1, 2, 3, 4),
		
		'filters' => array (
			0 => array (
				'dimension' => 'ENVIRONMENT',
				'operation' => 'include',
				'filters' => array (
					0 => array (
						'matchType' => 'id',
						'value' => '1',
						'label' => 'Mobile Web',
			        ),
					1 => array (
			          	'matchType' => 'id',
			          	'value' => '2',
				        'label' => 'Mobile App',
					),
					2 => array (
						'matchType' => 'id',
						'value' => '3',
						'label' => 'Desktop',
					),
					3 => array (
						'matchType' => 'id',
						'value' => '4',
						'label' => 'CTV',
					),
				),
		    ),
		),
		"metrics" => array(
			"OPPORTUNITIES",
			"IMPRESSIONS",
			"CPM",
			"REVENUE",
			"COST",
			"FORMAT_LOADS",
			"CLICKS",
			"FIRST_QUARTILES",
			"MIDPOINTS",
			"THIRD_QUARTILES",
			"COMPLETED_VIEWS",
			"AD_STARTS",
			"VIEWABLE_IMPRESSIONS"
		),
		'sort' => 
			array (
				0 => array (
					'field' => 'FORMAT_LOADS',
					'order' => 'desc',
				),
			),
		'offset' => 0,
		//'limit' => 10,
		'fileDownloadToken' => $fileDownloadToken
	);
	
	//print_r($post);
	//exit(0);
	$json = json_encode($post);

	$url = 'https://ui-api.lkqd.com/reports?definition=' . str_replace("America%5C%2FNew_York", "America%2FNew_York", urlencode($json));
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 0);
	//curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    
    $headers = array();
	$headers[] = 'Authority: ui-api.lkqd.com';
	$headers[] = 'Pragma: no-cache';
	$headers[] = 'Cache-Control: no-cache';
	$headers[] = 'Upgrade-Insecure-Requests: 1';
	$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36';
	$headers[] = 'Sec-Fetch-Mode: nested-navigate';
	$headers[] = 'Sec-Fetch-User: ?1';
	$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3';
	$headers[] = 'Sec-Fetch-Site: same-site';
	$headers[] = 'Referer: https://ui.lkqd.com/reports';
	//$headers[] = 'Accept-Encoding: gzip, deflate, br';
	$headers[] = 'Accept-Language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
	$result = curl_exec($ch);
	curl_close($ch);  

	//print_r($result);
	
	if(substr($result, 0, 4) == 'HTTP'){
		return false;
	}else{
		
		//$LogFileName = "complete_month.csv";
		//file_put_contents("/var/www/html/login/admin/lkqdimport/log/$LogFileName", $result);
		
		$N = 0;
		$CSVArray = array();
		$LineArray = array();
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $result) as $line){
			$LineArray = str_getcsv($line);
	    	$CSVArray[$N] = $LineArray;
	    	$N++;
		}
		
		//print_r($CSVArray);
		return $CSVArray;
		//exit(0);
	}
}

function getHourDataCSVSomeTags($STags, $DateFrom, $DateTo, int $HFrom, int $HTo){
	global $sessionId, $cookie_file;
	$uuid = gen_uuid();
	$fileDownloadToken = rand(100000,999999);
	
	$post = array(
		"whatRequest" => "csv",
		"uuid" => $uuid,
		"reportFormat" => "CSV",
		"includeSummary" => "false",
		"dateRangeType" => "TODAY",
		"startDate" => $DateFrom,
		"endDate" => $DateTo,
		"startDateHour" => $HFrom,
		"endDateHour" => $HTo,
		"startHour" => 0,
		"endHour" => 23,
		"timeDimension" => "HOURLY",
		"timezone" => "America/New_York",
		"reportType" => array("PARTNER", "SITE", "DOMAIN", "COUNTRY"),
		"environmentIds" => array(1, 2, 3, 4),
		
		'filters' => array (
			0 => array (
				'dimension' => 'ENVIRONMENT',
				'operation' => 'include',
				'filters' => array (
					0 => array (
						'matchType' => 'id',
						'value' => '1',
						'label' => 'Mobile Web',
			        ),
					1 => array (
			          	'matchType' => 'id',
			          	'value' => '2',
				        'label' => 'Mobile App',
					),
					2 => array (
						'matchType' => 'id',
						'value' => '3',
						'label' => 'Desktop',
					),
					3 => array (
						'matchType' => 'id',
						'value' => '4',
						'label' => 'CTV',
					),
				),
		    ),
		    1 => $STags,
		),
		"metrics" => array(
			"OPPORTUNITIES",
			"IMPRESSIONS",
			"CPM",
			"REVENUE",
			"COST",
			"FORMAT_LOADS",
			"CLICKS",
			"FIRST_QUARTILES",
			"MIDPOINTS",
			"THIRD_QUARTILES",
			"COMPLETED_VIEWS",
			"AD_STARTS",
			"VIEWABLE_IMPRESSIONS"
		),
		'sort' => 
			array (
				0 => array (
					'field' => 'FORMAT_LOADS',
					'order' => 'desc',
				),
			),
		'offset' => 0,
		//'limit' => 10,
		'fileDownloadToken' => $fileDownloadToken
	);
	
	//print_r($post);
	//exit(0);

	$json = json_encode($post);

	//$url = 'https://ui-api.lkqd.com/reports?definition=' . urlencode($json);
	$url = 'https://ui-api.lkqd.com/reports?definition=' . str_replace("America%5C%2FNew_York", "America%2FNew_York", urlencode($json));
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 0);
	//curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    
    $headers = array();
	$headers[] = 'Authority: ui-api.lkqd.com';
	$headers[] = 'Pragma: no-cache';
	$headers[] = 'Cache-Control: no-cache';
	$headers[] = 'Upgrade-Insecure-Requests: 1';
	$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36';
	$headers[] = 'Sec-Fetch-Mode: nested-navigate';
	$headers[] = 'Sec-Fetch-User: ?1';
	$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3';
	$headers[] = 'Sec-Fetch-Site: same-site';
	$headers[] = 'Referer: https://ui.lkqd.com/reports';
	//$headers[] = 'Accept-Encoding: gzip, deflate, br';
	$headers[] = 'Accept-Language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
	$result = curl_exec($ch);
	curl_close($ch);  

	//print_r($result);
	
	if(substr($result, 0, 4) == 'HTTP'){
		return false;
	}else{
	
		$N = 0;
		$CSVArray = array();
		$LineArray = array();
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $result) as $line){
			$LineArray = str_getcsv($line);
	    	$CSVArray[$N] = $LineArray;
	    	$N++;
		}
		
		//print_r($CSVArray);
		return $CSVArray;
		//exit(0);
	}
}

function getHourDataCSV($DateFrom, $DateTo, int $HFrom, int $HTo){
	global $sessionId, $cookie_file;
	$uuid = gen_uuid();
	$fileDownloadToken = rand(100000,999999);
	
	$post = array(
		"whatRequest" => "csv",
		"uuid" => $uuid,
		"reportFormat" => "CSV",
		"includeSummary" => "false",
		"dateRangeType" => "TODAY",
		"startDate" => $DateFrom,
		"endDate" => $DateTo,
		"startDateHour" => $HFrom,
		"endDateHour" => $HTo,
		"startHour" => 0,
		"endHour" => 23,
		"timeDimension" => "HOURLY",
		"timezone" => "America/New_York",
		"reportType" => array("PARTNER", "SITE", "DOMAIN", "COUNTRY"),
		"environmentIds" => array(1, 2, 3, 4),
		
		'filters' => array (
			0 => array (
				'dimension' => 'ENVIRONMENT',
				'operation' => 'include',
				'filters' => array (
					0 => array (
						'matchType' => 'id',
						'value' => '1',
						'label' => 'Mobile Web',
			        ),
					1 => array (
			          	'matchType' => 'id',
			          	'value' => '2',
				        'label' => 'Mobile App',
					),
					2 => array (
						'matchType' => 'id',
						'value' => '3',
						'label' => 'Desktop',
					),
					3 => array (
						'matchType' => 'id',
						'value' => '4',
						'label' => 'CTV',
					),
				),
		    ),
		),
		"metrics" => array(
			"OPPORTUNITIES",
			"IMPRESSIONS",
			"CPM",
			"REVENUE",
			"COST",
			"FORMAT_LOADS",
			"CLICKS",
			"FIRST_QUARTILES",
			"MIDPOINTS",
			"THIRD_QUARTILES",
			"COMPLETED_VIEWS",
			"AD_STARTS",
			"VIEWABLE_IMPRESSIONS"
		),
		'sort' => 
			array (
				0 => array (
					'field' => 'FORMAT_LOADS',
					'order' => 'desc',
				),
			),
		'offset' => 0,
		//'limit' => 10,
		'fileDownloadToken' => $fileDownloadToken
	);
	
	//print_r($post);
	//exit(0);

	$json = json_encode($post);

	//$url = 'https://ui-api.lkqd.com/reports?definition=' . urlencode($json);
	$url = 'https://ui-api.lkqd.com/reports?definition=' . str_replace("America%5C%2FNew_York", "America%2FNew_York", urlencode($json));
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 0);
	//curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    
    $headers = array();
	$headers[] = 'Authority: ui-api.lkqd.com';
	$headers[] = 'Pragma: no-cache';
	$headers[] = 'Cache-Control: no-cache';
	$headers[] = 'Upgrade-Insecure-Requests: 1';
	$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36';
	$headers[] = 'Sec-Fetch-Mode: nested-navigate';
	$headers[] = 'Sec-Fetch-User: ?1';
	$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3';
	$headers[] = 'Sec-Fetch-Site: same-site';
	$headers[] = 'Referer: https://ui.lkqd.com/reports';
	//$headers[] = 'Accept-Encoding: gzip, deflate, br';
	$headers[] = 'Accept-Language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
	$result = curl_exec($ch);
	curl_close($ch);  

	//print_r($result);
	
	if(substr($result, 0, 4) == 'HTTP'){
		return false;
	}else{
	
		$N = 0;
		$CSVArray = array();
		$LineArray = array();
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $result) as $line){
			$LineArray = str_getcsv($line);
	    	$CSVArray[$N] = $LineArray;
	    	$N++;
		}
		
		//print_r($CSVArray);
		return $CSVArray;
		//exit(0);
	}
}

function getDateDemandReportCSV($Date, int $HFrom, int $HTo){
	global $sessionId, $cookie_file;
	$uuid = gen_uuid();
	$fileDownloadToken = rand(100000,999999);
	
	$HTo = $HTo - 1;
	
	$post = array(
		"whatRequest" => "csv",
		"uuid" => $uuid,
		"reportFormat" => "CSV",
		"includeSummary" => false,
		"dateRangeType" => "TODAY",
		"startDate" => $Date,
		"endDate" => $Date,
		"startDateHour" => $HFrom,
		"endDateHour" => $HTo,
		"startHour" => 0,
		"endHour" => 23,
		"timeDimension" => "HOURLY",
		"timezone" => "America/New_York",
		"reportType" => array("TAG","DOMAIN"),
		"environmentIds" => array(1, 2, 3, 4),
		
		'filters' => array (
			0 => array (
				'dimension' => 'ENVIRONMENT',
				'operation' => 'include',
				'filters' => array (
					0 => array (
						'matchType' => 'id',
						'value' => '1',
						'label' => 'Mobile Web',
			        ),
					1 => array (
			          	'matchType' => 'id',
			          	'value' => '2',
				        'label' => 'Mobile App',
					),
					2 => array (
						'matchType' => 'id',
						'value' => '3',
						'label' => 'Desktop',
					),
					3 => array (
						'matchType' => 'id',
						'value' => '4',
						'label' => 'CTV',
					),
				),
		    ),
		),
		"metrics" => array("REQUESTS","IMPRESSIONS"),
		'sort' => 
			array (
				0 => array (
					'field' => 'REQUESTS',
					'order' => 'desc',
				),
			),
		'offset' => 0,
		//'limit' => 30,
		'fileDownloadToken' => $fileDownloadToken
	);
	
	$json = json_encode($post);
		
	$url = 'https://ui-api.lkqd.com/reports?definition=' . str_replace("America%5C%2FNew_York", "America%2FNew_York", urlencode($json));

	$ch = curl_init();	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
	
	$headers = array();
	$headers[] = 'Authority: ui-api.lkqd.com';
	$headers[] = 'Pragma: no-cache';
	$headers[] = 'Cache-Control: no-cache';
	$headers[] = 'Upgrade-Insecure-Requests: 1';
	$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36';
	$headers[] = 'Sec-Fetch-Mode: nested-navigate';
	$headers[] = 'Sec-Fetch-User: ?1';
	$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3';
	$headers[] = 'Sec-Fetch-Site: same-site';
	$headers[] = 'Referer: https://ui.lkqd.com/reports';
	$headers[] = 'Accept-Encoding: gzip, deflate, br';
	$headers[] = 'Accept-Language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
	    return false;
	}
	curl_close($ch);
	
	//echo $result;
	
	if(substr($result, 0, 4) == 'HTTP'){
		return false;
	}else{
	
		$N = 0;
		$CSVArray = array();
		$LineArray = array();
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $result) as $line){
			$LineArray = str_getcsv($line);
	    	$CSVArray[$N] = $LineArray;
	    	$N++;
		}
		
		return $CSVArray;
		
	}
}

function getSourcesByDealId(string $dealId) {
	global $sessionId, $cookie_file;
	$uuid = gen_uuid();

	$url = 'https://api.lkqd.com/supply-tags/find-by-id?tagId=' . $dealId;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

	$headers = [];
	$headers[] = 'Authority: ui-api.lkqd.com';
	$headers[] = 'Pragma: no-cache';
	$headers[] = 'Cache-Control: no-cache';
	$headers[] = 'Upgrade-Insecure-Requests: 1';
	$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36';
	$headers[] = 'Sec-Fetch-Mode: nested-navigate';
	$headers[] = 'Sec-Fetch-User: ?1';
	$headers[] = 'Accept: application/json, text/plain, */*';
	$headers[] = 'Sec-Fetch-Site: same-site';
	$headers[] = 'Referer: https://ui.lkqd.com/';
	$headers[] = 'Accept-Encoding: gzip, deflate, br';
	$headers[] = 'Accept-Language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$result = curl_exec($ch);
	if (curl_errno($ch)) {
	    return false;
	}
	curl_close($ch);
	return $result;
	if(substr($result, 0, 4) == 'HTTP'){
		return false;
	} else {
		return $result;
	}
}

function getCampaignDemandTagReportByDate($dealId, $campaignName, $startDate, $endDate) {
	global $sessionId, $cookie_file;
	$uuid = gen_uuid();
	
	$post = [
		"dateRangeType" => "CUSTOM",
		"endDate" => $endDate,
		"endDateHour" => 23,
		"endHour" => 23,
		"environmentIds" => [1, 2, 3, 4],
		"filters" => [
			0 => [
				"dimension" => "ENVIRONMENT",
				"operation" => "include",
				"filters" => [
					0 => [
						"matchType" => "id",
						"value" => "1",
						"label" => "Mobile Web",
					],
					1 => [
			          	"matchType" => "id",
			          	"value" => "2",
				        "label" => "Mobile App",
					],
					2 => [
						"matchType" => "id",
						"value" => "3",
						"label" => "Desktop",
					],
					3 => [
						"matchType" => "id",
						"value" => "4",
						"label" => "CTV",
					],
				],
			],
			1 => [
				"dimension" => "TAG",
				"operation" => "include",
				"filters" => [
					0 => [
						"label" => $campaignName,
						"matchType" => "id",
						"value" => $dealId,
					],
				],
			],
		],
		"includeSummary" => false,
		"metrics" => [
			"IMPRESSIONS",
			"COMPLETED_VIEWS",
			"VIEWABLE_IMPRESSIONS",
			"CLICKS"
		],
		"offset" => 0,
		"reportFormat" => "JSON",
		"reportType" => ["SITE", "TAG"],
		"sort" => [
			0 => [
				"field" => "IMPRESSIONS",
				"order" => "desc",
			],
		],
		"startDate" => $startDate,
		"startDateHour" => 0,
		"startHour" => 0,
		"timeDimension" => "OVERALL",
		"timezone" => "America/New_York",
		"uuid" => $uuid,
		"whatRequest" => "breakdown",
	];
	
	$json = json_encode($post);

	$url = 'https://ui-api.lkqd.com/reports?definition=' . str_replace("America%5C%2FNew_York", "America%2FNew_York", urlencode($json));

	$ch = curl_init();	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
	
	$headers = [];
	$headers[] = 'Authority: ui-api.lkqd.com';
	$headers[] = 'Pragma: no-cache';
	$headers[] = 'Cache-Control: no-cache';
	$headers[] = 'Upgrade-Insecure-Requests: 1';
	$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36';
	$headers[] = 'Sec-Fetch-Mode: nested-navigate';
	$headers[] = 'Sec-Fetch-User: ?1';
	$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3';
	$headers[] = 'Sec-Fetch-Site: same-site';
	$headers[] = 'Referer: https://ui.lkqd.com/reports';
	$headers[] = 'Accept-Encoding: gzip, deflate, br';
	$headers[] = 'Accept-Language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
	    return false;
	}
	curl_close($ch);
	
	if(substr($result, 0, 4) == 'HTTP'){
		return false;
	} else {
		return $result;
	}
}

function getAdvertiserDemandReportCSV($Date, $DemandTags, int $HFrom, int $HTo){
	global $sessionId, $cookie_file;
	$uuid = gen_uuid();
	$fileDownloadToken = rand(100000,999999);
	
	//$HTo = $HTo - 1;
	
	if(is_array($DemandTags)){
		if(count($DemandTags) == 0){
			echo "No demand tags 1";
			exit(0);
		}
	}else{
		echo "No demand tags 2";
		exit(0);
	}
	
	$FiltersArray = array();
	foreach($DemandTags as $DT){
		$FiltersArray[] = array(
			'matchType' => 'id',
			'value' => $DT
		);
	}
	
	$post = array(
		"whatRequest" => "csv",
		"uuid" => $uuid,
		"reportFormat" => "CSV",
		"includeSummary" => false,
		"dateRangeType" => "TODAY",
		"startDate" => $Date,
		"endDate" => $Date,
		"startDateHour" => $HFrom,
		"endDateHour" => $HTo,
		"startHour" => 0,
		"endHour" => 23,
		"timeDimension" => "HOURLY",
		"timezone" => "UTC",
		"reportType" => array("TAG"),
		"environmentIds" => array(1, 2, 3, 4),
		
		'filters' => array (
			0 => array (
				'dimension' => 'ENVIRONMENT',
				'operation' => 'include',
				'filters' => array (
					0 => array (
						'matchType' => 'id',
						'value' => '1',
						'label' => 'Mobile Web',
			        ),
					1 => array (
			          	'matchType' => 'id',
			          	'value' => '2',
				        'label' => 'Mobile App',
					),
					2 => array (
						'matchType' => 'id',
						'value' => '3',
						'label' => 'Desktop',
					),
					3 => array (
						'matchType' => 'id',
						'value' => '4',
						'label' => 'CTV',
					),
				),
		    )/*,
		    1 => array(
			    'dimension' => 'TAG',
			    'operation' => 'include',
			    'filters' => $FiltersArray
		    )*/
		),

		"metrics" => array("REQUESTS", "IMPRESSIONS", "VIEWABLE_IMPRESSIONS", "COMPLETED_VIEWS", "CLICKS", "REVENUE", "FIRST_QUARTILES", "MIDPOINTS", "THIRD_QUARTILES"),
		'sort' => 
			array (
				0 => array (
					'field' => 'REQUESTS',
					'order' => 'desc',
				),
			),
		'offset' => 0,
		//'limit' => 30,
		'fileDownloadToken' => $fileDownloadToken
	);
	
	//print_r($post);
	
	$json = json_encode($post);
		
	$url = 'https://ui-api.lkqd.com/reports?definition=' . str_replace("America%5C%2FNew_York", "America%2FNew_York", urlencode($json));

	$ch = curl_init();	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
	
	$headers = array();
	$headers[] = 'Authority: ui-api.lkqd.com';
	$headers[] = 'Pragma: no-cache';
	$headers[] = 'Cache-Control: no-cache';
	$headers[] = 'Upgrade-Insecure-Requests: 1';
	$headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36';
	$headers[] = 'Sec-Fetch-Mode: nested-navigate';
	$headers[] = 'Sec-Fetch-User: ?1';
	$headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3';
	$headers[] = 'Sec-Fetch-Site: same-site';
	$headers[] = 'Referer: https://ui.lkqd.com/reports';
	$headers[] = 'Accept-Encoding: gzip, deflate, br';
	$headers[] = 'Accept-Language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
	$result = curl_exec($ch);
	//echo $result;
	
	if (curl_errno($ch)) {
	    return false;
	}
	curl_close($ch);
	
	//echo $result;
	
	if(substr($result, 0, 4) == 'HTTP'){
		return false;
	}else{
	
		$N = 0;
		$CSVArray = array();
		$LineArray = array();
		foreach(preg_split("/((\r?\n)|(\r\n?))/", $result) as $line){
			$LineArray = str_getcsv($line);
	    	$CSVArray[$N] = $LineArray;
	    	$N++;
		}
		
		return $CSVArray;
		
	}
}

function myOperator($a, $b, $char) {
    switch($char) {
        case '>': 
        	if($a > $b){ return true; } else { return false; }
        case '<': 
        	if($a < $b){ return true; } else { return false; }
        case '>=':
        	if($a >= $b){ return true; } else { return false; }
        case '<=':
        	if($a <= $b){ return true; } else { return false; }
    }
}	

function takeMoney($Value){
	$Value = str_replace('$' , '', $Value);
	return $Value;
}

function takeComa($Value){
	$Value = str_replace(',' , '', $Value);
	return $Value;
}

function stopUpdate(){
	$myfile = fopen("/var/www/html/login/admin/lkqdimport/stop", "w");
	fwrite($myfile, time());
	fclose($myfile);
	exit(0);
}

function lockTable($Table){
	global $db;
	$sql = "UPDATE lock_tables SET Status = 1 WHERE TableName = '$Table' LIMIT 1";
	$db->query($sql);
}

function unlockTable($Table){
	global $db;
	$sql = "UPDATE lock_tables SET Status = 0 WHERE TableName = '$Table' LIMIT 1";
	$db->query($sql);
}

function isLocked($Table){
	global $db;
	$sql = "SELECT Status FROM lock_tables WHERE TableName = '$Table' LIMIT 1";
	$db->query($sql);
}

function getTableName($Date){
	$arD = explode('-', $Date);
	return 'reports' . $arD[0] . $arD[1];
}

function getTableNameResume($Date){
	$arD = explode('-', $Date);
	return 'reports_resume' . $arD[0] . $arD[1];
}

function checkTablesByDates($DateFrom, $DateTo){
	global $db;
	
	$Dates[0] = $DateFrom;
	$Dates[1] = $DateTo;
	
	foreach($Dates as $Date){
		$TableName = getTableName($Date);
		$TableNameResume = getTableNameResume($Date);
		
		$sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '" . $db->dbname . "' AND table_name = '" . $TableName . "' LIMIT 1";
		$chck = $db->getOne($sql);
		if($chck == 0){
			$TableStructure = file_get_contents('/var/www/html/login/admin/lkqdimport/reportstable.sql');
			$Table = str_replace('{{tablename}}', $TableName, $TableStructure);
			
			$db->query($Table);
		}
		
		$sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '" . $db->dbname . "' AND table_name = '" . $TableNameResume . "' LIMIT 1";
		$chck = $db->getOne($sql);
		if($chck == 0){
			$TableResumeStructure = file_get_contents('/var/www/html/login/admin/lkqdimport/reportstableresume.sql');
			$Table = str_replace('{{tablename}}', $TableNameResume, $TableResumeStructure);
			
			$db->query($Table);
		}
	}
}

function getSupplyPartner($supplyPartnerName)
{
	global $cookie_file;
	
	$URL = 'https://api.lkqd.com/supply/partners';
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch); 

	$Data = json_decode($result, true);

	if (!empty($Data["errorId"])) {
		return $Data["errorId"];
	}

	$supplyPartner = array_filter($Data, function ($partner) use ($supplyPartnerName) {
		return $partner["name"] === $supplyPartnerName;
	});

	if ($supplyPartner) {
		return array_column($supplyPartner, "supplyPartnerId")[0];
	}else{
		return "supply-partner-not-found";
	}
}

function getCreativity(int $partnerId, string $name) {
	global $cookie_file;
	
	$URL = "https://api.lkqd.com/demand/creatives?demandPartnerId={$partnerId}";
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch); 

	$data = json_decode($result, true);
	
	$creative = array_filter($data, function ($creativity) use ($partnerId, $name) {
		return $creativity["name"] === $name && $creativity["demandPartnerId"] === $partnerId;
	});

	if ($creative) {
		return array_column($creative, "creativeId")[0];
	}else{
		return "creative-not-found";
	}
}

function updateCreativity(int $partnerId, int $creativityId, string $name, string $type) {
	global $cookie_file;

	$URL = 'https://api.lkqd.com/demand/creatives/' . $creativityId;

	$payload = [
		"demandPartnerId" => $partnerId,
		"mediaType" => $type,
		"name" => $name
	];

	$payloadJson = json_encode($payload);
	
	$Headers = [
		'Authority: ui-api.lkqd.com',
		'Method: PUT',
		'Path: /demand/creatives/' . $creativityId,
		'Scheme: https',
		'Accept: application/json, text/plain, */*',
		'Accept-Language: en-US,en;q=0.9,es;q=0.8',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'Sec-Ch-Ua-Mobile: ?0',
		'Sec-Fetch-Mode: cors',
		'Sec-Fetch-Site: same-site',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch);

	$data = json_decode($result, false);

	if(!empty($data->errors)){
		return $data->errors;
	}

	return true;
}

function newCreativity(int $partnerId, string $type, string $name) {
	global $cookie_file;

	$URL = 'https://api.lkqd.com/demand/creatives';

	$payload = [
		"demandPartnerId" => $partnerId,
		"mediaType" => $type,
		"name" => $name,
	];

	$payloadJson = json_encode($payload);
	
	$Headers = [
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	];
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch); 

	$response = json_decode($result);

	if (!array_key_exists('creativeId', $response)) {
		return $response->errorId;
	}

	return $response->creativeId;
}

function uploadCreativityVideo(int $creativityId, $file) {
	global $cookie_file;

	$URL = "https://api.lkqd.com/demand/creatives/{$creativityId}/upload-video";

	if (function_exists('curl_file_create')) {
		$cFile = curl_file_create($file['tmp_name'], $file['type'], $file['name']);
	} else {
		$cFile = '@' . realpath($file['tmp_name']);
	}

	$payload = [
		'creativeId' => $creativityId,
		'file' => $cFile
	];

	$Headers = [
		'Accept: application/json, text/plain, */*',
		'Content-Type: multipart/form-data',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch);

	return $result;
}

function getSupplySource(int $partnerId, string $name)
{
	global $cookie_file;
	
	$URL = 'https://api.lkqd.com/supply/sources?includes=siteId,siteName,partnerId,status';
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch); 

	$Data = json_decode($result, true);
	
	$supplySource = array_filter($Data, function ($source) use ($partnerId, $name) {
		return $source["siteName"] === $name && $source["partnerId"] === $partnerId;
	});

	if ($supplySource) {
		return array_column($supplySource, "siteId")[0];
	}else{
		return "supply-source-not-found";
	}
}

function getDemandPartner($supplyPartnerName)
{
	global $cookie_file;
	
	$URL = 'https://ui-api.lkqd.com/demand/tree';
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'lkqd-api-version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch);

	$data = json_decode($result, true);
	
	$demandPartner = array_filter($data["data"], function ($partner) use ($supplyPartnerName) {
		return $partner["sourceName"] === $supplyPartnerName;
	});

	if ($demandPartner) {
		return array_column($demandPartner, "sourceId")[0];
	}else{
		return "demand-partner-not-found";
	}
}

/**
 * Function to get creativity if given it's tag id
 */
function getTagCreativityId(int $tagId)
{
	global $cookie_file;
	
	$URL = sprintf('https://api.lkqd.com/demand/creatives/tag-associations?tagId=%d', $tagId);
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'lkqd-api-version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch);

	if (false !== strpos($result, 'Forbidden') || false !== strpos($result, 'tagId must be specified')) {
		http_response_code(404);
		return 'No creativity found with the given tag id!';
	}

	if (false !== strpos($result, 'authorization')) {
		http_response_code(403);
		return UNAUTHORIZED_PREFIX;
	}

	$response = json_decode($result, true);

	if (array_key_exists('errorId', $response)) {
		http_response_code(404);
		return $response['message'];
	}

	http_response_code(200);
	return $response[0]['creativeId'];
}

function getAgenciesData(): array
{
	global $cookie_file;
	
	$URL = 'https://ui-api.lkqd.com/demand/tree';
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'lkqd-api-version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch);

	if ($result === "HTTP method not allowed, supported methods: OPTIONS") {
		return [UNAUTHORIZED_PREFIX];
	}

	$response = json_decode($result, true);

	return $response;
}

function getOrder(int $partnerId, string $name)
{
	global $cookie_file;
	
	$URL = "https://api.lkqd.com/demand/orders?demandPartnerId={$partnerId}";
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch); 

	$data = json_decode($result, true);
	
	$partnerOrder = array_filter($data, function ($order) use ($partnerId, $name) {
		return $order["demandPartnerId"] === $partnerId && $order["name"] === $name;
	});

	if ($partnerOrder) {
		return array_column($partnerOrder, "orderId")[0];
	}else{
		return "order-not-found";
	}
}

function newOrder(int $sourceId, string $name) {
	global $cookie_file;

	$URL = 'https://ui-api.lkqd.com/orders';

	$payload = [
		"orderId" => null,
		"orderName" => $name,
		"sourceId" => $sourceId,
		"thirdPartyOrderId" => null,
	];

	$payloadJson = json_encode($payload);
	
	$Headers = [
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'lkqd-api-version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	];
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch);

	$data = json_decode($result);

	if(!empty($data->errors)){
		return $data->errors;
	}

	return $data->data->orderId;
}

function updateOrder(int $orderId, int $sourceId, string $name) {
	global $cookie_file;

	$URL = 'https://ui-api.lkqd.com/orders/' . $orderId;

	$payload = [
		"orderId" => $orderId,
		"orderName" => $name,
		"sourceId" => $sourceId,
		"thirdPartyOrderId" => null
	];

	$payloadJson = json_encode($payload);
	
	$Headers = [
		'Authority: ui-api.lkqd.com',
		'Method: PUT',
		'Path: /orders/' . $orderId,
		'Scheme: https',
		'Accept: application/json, text/plain, */*',
		'Accept-Language: en-US,en;q=0.9,es;q=0.8',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'lkqd-api-version: 88',
		'Sec-Ch-Ua-Mobile: ?0',
		'Sec-Fetch-Mode: cors',
		'Sec-Fetch-Site: same-site',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch);

	$data = json_decode($result, false);

	if(!empty($data->errors)){
		return $data->errors;
	}

	return true;
}

function newDemandPartner(string $name) {
	global $cookie_file;

	$URL = 'https://api.lkqd.com/demand/partners';

	$payload = [
		"assignedUserId" => null,
		"metrics" => [
			"REQUESTS",
			"IMPRESSIONS",
			"REVENUE",
			"CTR",
			"FIRST_QUARTILE_RATE",
			"MIDPOINT_RATE",
			"THIRD_QUARTILE_RATE",
			"VTR",
			"CLICKS",
			"FIRST_QUARTILES",
			"MIDPOINTS",
			"THIRD_QUARTILES",
			"COMPLETED_VIEWS",
		],
		"name" => $name,
	];

	$payloadJson = json_encode($payload);
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	die($result);
	curl_close($ch); 

	$response = json_decode($result);
	
	if (array_key_exists('demandPartnerId', $response)) {
		return $response->demandPartnerId;
	}

	return $response->errorId;
}

function updateDemandPartner(int $demandPartnerId, string $name) {
	global $cookie_file;

	$URL = 'https://api.lkqd.com/demand/partners/' . $demandPartnerId;

	$payload = [
		"assignedUserId" => null,
		"metrics" => [
			"adds" => [],
			"removes" => [],
		],
		"name" => $name,
	];

	$payloadJson = json_encode($payload);
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch);

	$data = json_decode($result, false);

	if(!empty($data) && property_exists($data, 'errorId')){
		return $data->errorId;
	}

	return true;
}

function getTagInfo(int $tagId): array
{
	global $cookie_file;

	$url = "https://ui-api.lkqd.com/tags/" . $tagId;

	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'LKQD-Api-Version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	error_log('gadiel --- ' .$result);
	curl_close($ch);

	if ('HTTP method not allowed, supported methods: OPTIONS' === $result) {
		http_response_code(403);
		return [UNAUTHORIZED_PREFIX];
	}

	$response = json_decode($result, true);

	if (!empty($response->errors)) {
		return $response->errors;
	}

	http_response_code(200);
	return $response['data'];
}

/**
 * Fuction to get deal info from LKQD.
 */
function getDealInfo(int $dealId): array {
	global $cookie_file;

	$url = "https://ui-api.lkqd.com/deals/" . $dealId;

	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'LKQD-Api-Version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch);

	if ('HTTP method not allowed, supported methods: OPTIONS' === $result) {
		return [UNAUTHORIZED_PREFIX];
	}

	$response = json_decode($result, true);

	if (!empty($response->errors)) {
		return $response->errors;
	}

	return $response['data'];
}

/**
 * Function to update demand tag status on LKQD
 * 
 * @param int $demandTagId
 * @param int $status active or inactive
 */
function updateDemandTagStatus(int $demandTagId, string $status) {
	global $cookie_file;

	$url = sprintf("https://ui-api.lkqd.com/tags/%d/status", $demandTagId);

	$headers = [
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'LKQD-Api-Version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	];

	$payload = ["status" => $status];
	$jsonPayload = json_encode($payload);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch);

	if ($result === "HTTP method not allowed, supported methods: OPTIONS") {
		return UNAUTHORIZED_PREFIX;
	}

	$response = json_decode($result);

	if (!empty($response->errors)) {
		return json_encode($response->errors);
	}

	return $response->status;
}

/**
 * Function to get the deal name from the report server DB
 */
function getDealName(int $dealId): string {
	global $db;

	$sql = <<<SQL
SELECT
	name
FROM
	campaign
WHERE
	deal_id = {$dealId}
SQL;

	$dealName = $db->getOne($sql);

	return empty($dealName) ? '' : $dealName;
}

function isValidDate(string $date): bool
{
	return strtotime($date);
}

/**
 * Function that returns date range type as needed for LKQD
 */
function getDateRangeType(string $startDate, string $endDate): string {

	if (!isValidDate($startDate) || !isValidDate($endDate)) {
		return 'No valid dates!';
	}

	$start = new DateTime($startDate);
	$end = new DateTime($endDate);
	$today = new DateTime('NOW');
	$yesterday = new DateTime('NOW');
	$yesterday->sub(new DateInterval('P1D'));
	$dayBeforeYesterday = new DateTime('NOW');
	$dayBeforeYesterday->sub(new DateInterval('P2D'));
	$firstDayOfThisMonth = new DateTime('NOW');
	$firstDayOfThisMonth->modify('first day of this month');
	$lastDayOfThisMonth = new DateTime('NOW');
	$lastDayOfThisMonth->modify('last day of this month');
	$firstDayOfLastMonth = new DateTime('NOW');
	$firstDayOfLastMonth->modify('first day of last month');
	$lastDayOfLastMonth = new DateTime('NOW');
	$lastDayOfLastMonth->modify('last day of last month');
	$diff = $end->diff($start);

	if ($start->format('Y-m-d') === $today->format('Y-m-d') &&
		$end->format('Y-m-d') === $today->format('Y-m-d')
	) {
		return 'TODAY';
	} elseif ($start->format('Y-m-d') === $yesterday->format('Y-m-d') &&
		$end->format('Y-m-d') === $yesterday->format('Y-m-d')
	) {
		return 'YESTERDAY';
	} elseif ($start->format('Y-m-d') === $dayBeforeYesterday->format('Y-m-d') &&
		$end->format('Y-m-d') === $dayBeforeYesterday->format('Y-m-d')
	) {
		return 'DAY_BEFORE_YESTERDAY';
	} elseif ($end->format('Y-m-d') === $today->format('Y-m-d') && $diff->days === 6) {
		return 'PAST_7_DAYS';
	} elseif ($end->format('Y-m-d') === $yesterday->format('Y-m-d') && $diff->days === 6) {
		return '7_DAYS_BEFORE_TODAY';
	} elseif ($end->format('Y-m-d') === $today->format('Y-m-d') && $diff->days === 29) {
		return 'PAST_30_DAYS';
	} elseif ($start->format('Y-m-d') === $firstDayOfThisMonth->format('Y-m-d') &&
		$end->format('Y-m-d') === $lastDayOfThisMonth->format('Y-m-d')
	) {
		return 'THIS_MONTH';
	} elseif ($start->format('Y-m-d') === $firstDayOfLastMonth->format('Y-m-d') &&
		$end->format('Y-m-d') === $lastDayOfLastMonth->format('Y-m-d')
	) {
		return 'LAST_MONTH';
	} else {
		return 'CUSTOM';
	}
}

/**
 * Function to get top ten domains of a deal on LKQD
 * 
 * @return array of top 10 domains of a deal
 */
function getTopDealDomains(int $dealId, string $startDate, string $endDate): array {
	global $cookie_file;
	$uuid = gen_uuid();

	$url = 'https://ui-api.lkqd.com/reports';

	$headers = [
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'LKQD-Api-Version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36',
	];

	$payload = [
		"whatRequest" => "breakdown",
		"uuid" => $uuid,
		"reportFormat" => "JSON",
		"includeSummary" => true,
		"dateRangeType" => getDateRangeType($startDate, $endDate),
		"startDate" => $startDate,
		"endDate" => $endDate,
		"startDateHour" => 0,
		"endDateHour" => 23,
		"startHour" => 0,
		"endHour" => 23,
		"timeDimension" => "OVERALL",
		"timezone" => "America/New_York",
		"reportType" => [
			"TAG",
			"DOMAIN"
		],
		"environmentIds" => [
			1,
			2,
			3,
			4
		],
		"filters" => [
			[
			  "dimension" => "ENVIRONMENT",
			  "operation" => "include",
			  "filters" => [
				[
				  "matchType" => "id",
				  "value" => "1",
				  "label" => "Mobile Web"
				],
				[
				  "matchType" => "id",
				  "value" => "2",
				  "label" => "Mobile App"
				],
				[
				  "matchType" => "id",
				  "value" => "3",
				  "label" => "Desktop"
				],
				[
				  "matchType" => "id",
				  "value" => "4",
				  "label" => "CTV"
				]
			  ]
			],
			[
			  "dimension" => "TAG",
			  "operation" => "include",
			  "filters" => [
				[
				  "matchType" => "id",
				  "value" => $dealId,
				  "label" => getDealName($dealId),
				]
			  ]
			]
		  ],
		  "metrics" => [
			"COMPLETED_VIEWS"
		  ],
		  "sort" => [
			[
			  "field" => "COMPLETED_VIEWS",
			  "order" => "desc"
			]
		  ],
		  "offset" => 0,
		  "limit" => 500
	];

	$payloadJson = json_encode($payload);

	$ch = curl_init();	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
	$result = curl_exec($ch);
	curl_close($ch);

	if (false !== strpos($result, 'HTTP method not allowed, supported methods: OPTIONS')) {
		return [UNAUTHORIZED_PREFIX];
	}
	
	$data = json_decode($result);

	if (!empty($data->errors)) {
		return $data->errors;
	}

	$domains = $data->data->entries;
	$topTenDomainsArray = array_slice($domains, 0, 10);
	$topTenDomains = array_map(function ($domain) {
		return $domain->dimension2Name;
	}, $topTenDomainsArray);
	
	return $topTenDomains;
}

function getDeal(int $orderId, string $name) {
	global $cookie_file;
	
	$URL = 'https://ui-api.lkqd.com/demand/tree';
	
	$Headers = [
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'LKQD-Api-Version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	];
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch); 

	$data = json_decode($result, true);

	$dealId = "deal-not-found";

	foreach ($data["data"] as $partner) {
		foreach ($partner["orders"] as $order) {
			foreach ($order["deals"] as $deal) {
				if ($order["orderId"] === $orderId && $deal["dealName"] === $name) {
					$dealId = $deal["dealId"];
				} 
			}
		}
	}

	return $dealId;
}

/**
 * Function for getting a demand tag id from LKQD
 */
function getDemandTagId(int $dealId, string $name): string {
	global $cookie_file;
	
	$URL = 'https://ui-api.lkqd.com/demand/tree';
	
	$Headers = [
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'LKQD-Api-Version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	];
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch);

	if ('HTTP method not allowed, supported methods: OPTIONS' === $result) {
		http_response_code(403);
		return UNAUTHORIZED_PREFIX;
	}

	$data = json_decode($result, true);

	$demandTagId = "demand-tag-not-found";

	foreach ($data["data"] as $partner) {
		foreach ($partner["orders"] as $order) {
			foreach ($order["deals"] as $deal) {
				foreach ($deal["tags"] as $tag) {
					if ($tag["dealId"] === $dealId && $tag["tagName"] === $name) {
						$demandTagId = $tag["tagId"];
					} 
				}
			}
		}
	}

	http_response_code(200);
	return $demandTagId;
}

function newOrUpdateDeal(
	int $orderId,
	string $name,
	string $startDate,
	string $endDate,
	string $deliveryPacing,
	int $goal,
	string $status,
	int $dealId = null,
	int $freqCap = null
) {
	global $cookie_file;
error_log($orderId);
error_log($name);
error_log($startDate);
error_log($endDate);
error_log($deliveryPacing);
error_log($goal);
error_log($status);
error_log($dealId);
error_log($freqCap);
	$URL = 'https://ui-api.lkqd.com/deals';

	$deliveryPacings = [
		"1" => 3,
		"2" => 8,
		"3" => 7,
	];

	$frequencyCap = [];

	if ($freqCap) {
		$frequencyCap = [
			[
				"eventId" => 3,
				"eventName" => "impression",
				"timePeriod" => "day",
				"timePeriodCount" => 1,
				"capCount" => $freqCap
			]
		];
	}

	$payload = [
		"dealId" => $dealId,
		"orderId" => $orderId,
		"name" => $name,
		"dealType" => "normal",
		"adType" => "video",
		"status" => $status,
		"tier" => 1,
		"weight" => null,
		"cpm" => 0,
		"cpmType" => "fixed",
		"cost" => 0,
		"costRev" => 0,
		"costCpm" => 0,
		"costType" => "none",
		"caps" => [],
		"frequencyCaps" => $frequencyCap,
		"pacings" => [
			[
				"eventId" => $deliveryPacings[$deliveryPacing],
				"pacingPeriod" => "day",
				"pacingType" => "throttled-even",
				"timezone" => "UTC",
				"goal" => $goal,
				"frontLoadRatio" => null
			]
		],
		"frequencyCapKey" => "any_user_id",
		"frequencyCapNoUid" => "use_ip",
		"activeTagCount" => null,
		"totalTagCount" => null,
		"budget" => null,
		"appliedRestrictoListsData" => [
			"appListType" => null,
			"appSelectedListType" => null,
			"appListId" => null,
			"appListAllowUnknownApps" => 0,
			"bundleIdListType" => null,
			"bundleIdListId" => null,
			"bundleIdListAllowUnknownBundleIds" => 0,
			"bundleIdListBlockDetectedMismatch" => 0,
			"creativeGatingListId" => null,
			"creativeGatingListDescription" => null,
			"creativeGatingListConditions" => null,
			"creativeGatingListAllowUnknownCreativeGatingIds" => 0,
			"deviceIdListType" => null,
			"deviceIdListId" => null,
			"deviceIdListAllowUnknownDeviceIds" => 0,
			"domainListType" => null,
			"domainListId" => null,
			"domainListAllowUnknownDomains" => 0,
			"domainListApplyDetected" => 0,
			"domainListBlockDetectedMismatch" => 0,
			"ipListType" => null,
			"ipListId" => null,
			"ipListAllowUnknownIps" => 0
		],
		"trackingPixels" => [],
		"pmpSiteId" => null,
		"daypartStatus" => "inactive",
		"daypartTimezone" => "America/New_York",
		"daypartEntries" => [],
		"startTs" => $startDate . " 00:00:00.0",
		"endTs" => $endDate . " 23:59:59.0",
		"customFlightTimeZone" => "UTC"
	];

	$payloadJson = json_encode($payload);

	$Headers = [
		'Authority: ui-api.lkqd.com',
		'Method: POST',
		'Path: /deals',
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'LKQD-Api-Version: 88',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'Sec-Fetch-Mode: cors',
		'Sec-Fetch-Site: same-site',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	];
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);

	curl_close($ch); 

	$response = json_decode($result);

	if (!empty($response->errors)) {
		return json_encode($response->errors);
	}

	return $response->data->dealId;
}

/**
 * Function to select propvided sources to a demand tag and unselect the rest of the sources.
 */
function keepDemandTagsSelected(array $tags): bool {
	global $cookie_file;

	$URL = 'https://api.lkqd.com/supply-tags/update-db-associations';

	$sources = getSources();
	if (UNAUTHORIZED_PREFIX === $sources) {
		logIn('KeepDemandTagsSelected function');
		$sources = getSources();
	}
	$additionIds = [];

	$payload = [
		"removeAssociations" => [],
		"addAssociations" => [],
		"updateAssociations" => []
	];

	foreach ($tags as $tag) {
		foreach ($tag["sources"] as $source) {
			$payload["addAssociations"][] = [
				"siteId" => $source,
				"tagId" => $tag["demand_id"],
			];
			$additionIds[] = $source;
		}
	}

	foreach ($sources as $source) {
		if (!in_array($source->siteId, $additionIds)) {
			$payload["removeAssociations"][] = [
				"siteId" => $source->siteId,
				"tagId" => $tag["demand_id"],
			];
		}
	}

	$payloadJson = json_encode($payload);

	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'LKQD-Api-Version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch);

	$data = json_decode($result, false);

	if(!empty($data) && property_exists($data, 'errorId')){
		return $data->errorId;
	}

	return true;
}

function unselectDemandTags(array $tags) {
	global $cookie_file;

	$URL = 'https://api.lkqd.com/supply-tags/update-db-associations';

	$payload = [
		"removeAssociations" => [],
		"addAssociations" => [],
		"updateAssociations" => []
	];

	foreach ($tags as $tag) {
		foreach ($tag["sources"] as $source) {
			$payload["removeAssociations"][] = [
				"siteId" => $source,
				"tagId" => $tag["demand_id"],
			];
		}
	}

	$payloadJson = json_encode($payload);

	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'LKQD-Api-Version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch);

	$data = json_decode($result, false);

	if(!empty($data) && property_exists($data, 'errorId')){
		http_response_code(403);
		return $data->errorId;
	}

	http_response_code(200);
	return true;
}

function newDomain($name) {
	global $db, $db2;
	
	$sql = "SELECT * FROM reports_domain_names WHERE Name = '{$name}' LIMIT 1";
	$domainId = intval($db->getOne($sql));

	if ($domainId == 0) {
		$sql = "INSERT INTO reports_domain_names (Name) VALUES ('{$name}')";
		$db->query($sql);
		$domainId = mysqli_insert_id($db->link);

		$sql = "SELECT * FROM report_domain WHERE id = $domainId LIMIT 1";
		$reportDomainId = intval($db2->getOne($sql));

		if ($reportDomainId == 0) {
			$sql = "INSERT INTO report_domain (id, name, is_alexa_rank_scanned, page_per_visit) VALUES ($domainId, '{$name}', 0, 0)";
		} else {
			$sql = "UPDATE report_domain SET name = '{$name}' WHERE id = $reportDomainId";
		}

		$db2->query($sql);
	}

	return $domainId;
}

/**
 * Function to retrieve from LKQD the sources
 */
function getSources() {
	global $cookie_file;

	$sourcesUrl = "https://api.lkqd.com/supply/sources/fetch";
	$sourcesPayload = [
		"includes" => [
			"siteId",
			"siteName",
			"status",
			"environmentId",
			"partnerId",
			"siteCostType",
			"siteCost",
			"cpmFloorDemand",
			"demandTargetingType",
			"adType"
		]
	];

	$sourcePayloadJson = json_encode($sourcesPayload);

	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'LKQD-Api-Version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $sourcesUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $sourcePayloadJson);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch);
	$response = json_decode($result, false);

	if (!empty($response->errorId)) {
		return $response->errorId;
	}
	
	return $response;
}

/**
 * Function to build array of additions sources as LKQD is expecting.
 */
function getAdditions(array $sources): array {
	$additions = [];
	foreach ($sources as $source) {
		$additions[] = [
			"siteId" => $source->siteId,
			"tagId" => null,
			"priority" => null,
		];
	}

	return $additions;
}

/**
 * Function to build array of tracking pixels as LKQD is expecting.
 */
function getTrackingPixels(string $trackingPixels): array {
	$pixels = [];
	$trackingPixelsDecoded = json_decode($trackingPixels, true);
	foreach($trackingPixelsDecoded as $pixel) {
		$pixels[] = [
			"eventId" => $pixel['event_id'],
			"pixelUrl" => $pixel['pixel_url'],
			"supplyScope" => null,
			"pixelId" => null
		];
	}

	return $pixels;
}

/**
 * Fuction to build array of add associations as LKQD is expecting.
 */
function getAddAssociations(array $sources, int $demandTagId): array {
	$associations = [];
	foreach($sources as $source) {
		$associations[] = [
			"siteId" => $source->siteId,
			"tagId" => $demandTagId,
			"priority" => null,
		];
	}

	return $associations;
}

/**
 * Function to build array of environments as LKQD is expecting.
 */
function getEnvironments(array $environments): array {
	$envs = [];
	foreach ($environments as $environment) {
		$envs[] = ["id" => $environment];
	}

	return $envs;
}

/**
 * Function to build string encoded as LKQD is expecting.
 */
function getGeoTargetingData(array $countries): string {
	$children = [];
	foreach ($countries as $country) {
		$children[] = [
			"id" => $country,
			"entity" => "country"
		];
	}

	$geoTargetingData = [
		"relationshipType" => "or",
		"children" => [
			[
				"relationshipType" => "and",
				"children" => [
					[
						"relationshipType" => "or",
						"children" => $children
					]
				]
			]
		],
		"_countyNames" => [],
		"_cityNames" => []
	];

	return json_encode($geoTargetingData);
}

function updateCreative(
	int $demandTagId,
	int $dealId,
	string $name,
	string $status,
	string $clickThroughUrl,
	string $trackingPixels,
	int $creativeId,
	string $environments,
	string $countries,
	int $type,
	string $demandTagUrl
) {
	global $cookie_file;

	$sources = getSources();
	if (UNAUTHORIZED_PREFIX === $sources) {
		logIn('updateCreative function');
		$sources = getSources();
	}
	$envs = json_decode($environments, true);
	$geoTargetingData = getGeoTargetingData(json_decode($countries, true));
	$envsArray = getEnvironments($envs);
	$dealInfo = getDealInfo($dealId);
	if (in_array(UNAUTHORIZED_PREFIX, $dealInfo)) {
		logIn('updateCreative function');
		$dealInfo = getDealInfo($dealId);
	}

	$filteredSources = array_filter($sources, function ($source) use ($envs) {
		return $source->cpmFloorDemand >= 0.2 && in_array($source->environmentId, $envs);
	});

	$additions = getAdditions($filteredSources);
	$pixels = getTrackingPixels($trackingPixels);

	$headers = [
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'LKQD-Api-Version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	];

	$tagAssociationsUrl = "https://api.lkqd.com/demand/creatives/tag-associations";
	$tagAssociationsPayload = [
		"adds" => [
			[
			  "tagId" => $demandTagId,
			  "creativeId" => $creativeId
			]
		  ],
		"removes" => []
	];

	$tagAssociationsPayloadJson = json_encode($tagAssociationsPayload);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $tagAssociationsUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $tagAssociationsPayloadJson);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch);

	$url = 'https://ui-api.lkqd.com/tags';

	$levels = [];
	for ($j = 1; $j <= 40; $j++) {
		$levels[] = [
			"levelNum" => $j,
			"targetingType" => null,
			"parameters" => []
		];
	}

	$payload = getDemandTagPayload(
		$demandTagId,
		$dealInfo,
		$dealId,
		$name,
		$status,
		$envsArray,
		$geoTargetingData,
		$levels,
		$clickThroughUrl,
		$additions,
		$pixels,
		$type,
		$demandTagUrl
	);

	$payloadJson = json_encode($payload);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch);

	$response = json_decode($result, true);

	if (!empty($response['errors'])) {
		return $response['errors'];
	}

	return $response['status'];
}

/**
 * function to set inactive when creating a new tag to avoid error on creating a tag. 
 */
function getDemandTagStatus(int $demandTagId, $status): string
{
	return $demandTagId > 0 ? $status : 'inactive';
}

/**
 * function to return demand tag id for edition or null for creation.
 */
function getTagId(int $tagId)
{
	return $tagId > 0 ? $tagId : null;
}

/**
 * Function to get demand tag type on LKQD.
 */
function getTagtype(int $type): string
{
	return isVideo($type) ? "lkqd-hosted" : "vast";
}

/**
 * Function to get tag source on LKQD.
 */
function getTagSource(int $type): string
{
	return isVideo($type) ? "lkqd" : "other";
}

function isVideo(int $type): bool
{
	return TAG_TYPE_VIDEO === $type;
}

/**
 * Function to get ad delivery type depending on tag type.
 */
function getAdDeliveryType(int $type): string
{
	return isVideo($type) ? "guaranteed" : "non-guaranteed";
}

/**
 * Function to get tag player size targeting method depending on tag type.
 */
function getPlayerSizeTargetingMethod(int $type)
{
	return isVideo($type) ? "detected" : null;
}

/**
 * Function to get tag player size targeting allow undetectable depending on tag type.
 */
function getPlayerSizeTargetingAllowUndetectable(int $type)
{
	return isVideo($type) ? 1 : null;
}

/**
 * Function to get ad tag depending on tag type.
 */
function getAdTag(int $type, string $demandTagUrl)
{
	return isVideo($type) ? null : $demandTagUrl;
}

/**
 * Function to get programmatic buyer private deal tag depending on tag type.
 */
function getIsProgrammaticBuyerPrivateDealTag(int $type)
{
	return isVideo($type) ? null : 0;
}

/**
 * Function to get tag click through url depending on tag type.
 */
function getClickthroughUrl(int $type, string $clickThroughUrl)
{
	return isVideo($type) ? $clickThroughUrl : null;
}

/**
 * Function to get tag weight depending on tag type.
 */
function getTagWeight(int $type)
{
	return isVideo($type) ? 100 : null;
}

/**
 * Function to build array payload for a demand tag as LKQD is expecting.
 */
function getDemandTagPayload(
	int $demandTagId,
	array $dealInfo,
	int $dealId,
	string $name,
	string $status,
	array $envsArray,
	string $geoTargetingData,
	array $levels,
	string $clickThroughUrl,
	array $additions,
	array $pixels,
	int $type,
	string $demandTagUrl
) {
	return [
		"tagId" => getTagId($demandTagId),
		"dealCpm" => 0,
		"dealCpmType" => $dealInfo['cpmType'],
		"dealId" => $dealId,
		"dealTier" => 1,
		"dealStatus" => $dealInfo['status'],
		"name" => $name,
		"status" => getDemandTagStatus($demandTagId, $status),
		"tagType" => getTagtype($type),
		"tagSource" => getTagSource($type),
		"caps" => [],
		"frequencyCaps" => [],
		"frequencyCapKey" => null,
		"frequencyCapNoUid" => $dealInfo['frequencyCapNoUid'],
		"environments" => $envsArray,
		"adType" => "video",
  		"adDeliveryType" => getAdDeliveryType($type),
		"targeting" => [
			"deviceOses" => [],
			"browserTargetingEntries" => [],
			"connectionTypeTargetingEntries" => [],
			"behavioralTargeting" => [
				"relationshipTypeBetweenProfiles" => null,
				"profiles" => []
			],
			"playerFormatTargetingEntries" => [],
			"geoTargetingData" => $geoTargetingData,
			"deviceOsTargetingType" => null,
			"browserTargetingType" => null,
			"connectionTypeTargetingType" => null,
			"playerTargetingType" => null,
			"playerTargetingEntries" => [],
			"playerFormatTargetingType" => null,
			"lkqdAccountTargetingType" => "disabled",
			"supplyTagTargetingType" => null,
			"supplyTagTargetingEntries" => [],
			"viewabilityTargeting" => null,
			"volumeTargetingType" => null,
			"volumeTargeting" => null,
			"performanceTargeting" => [
				"vtr" => null,
				"ctr" => null,
				"viewabilityMeasured" => null,
				"viewabilityRate" => null,
				"lkqdEstimatedInvalid" => null,
				"moatSuspiciousBot" => null,
				"whiteOpsIvt" => null,
				"whiteOpsSivt" => null,
				"minDaysLive" => null,
				"efficiencyRate" => null,
				"moatGroupm" => null,
				"mpEfficiencyRate" => null,
				"moatVerifiedImps" => null,
				"whiteopsVerifiedImps" => null,
				"moatHumanAvoc" => null,
				"whiteopsIncompleteLoad" => null,
				"dvMonitoredImpressions" => null,
				"dvFraudRate" => null
			],
			"customParameterTargeting" => [
				"levels" => $levels
			],
			"playerSizeTargeting" => [
				"method" => getPlayerSizeTargetingMethod($type),
				"allowUndetectable" => getPlayerSizeTargetingAllowUndetectable($type),
				"environmentChoice" => "same-for-all-environments",
				"widthHeightSpecifications" => [
					"all" => [
						"environmentId" => null,
						"targetingType" => null,
						"relationshipType" => null,
						"targetingEntries" => []
					  ],
					  "mobileWeb" => [
						"environmentId" => 1,
						"targetingType" => null,
						"relationshipType" => null,
						"targetingEntries" => []
					  ],
					  "mobileApp" => [
						"environmentId" => 2,
						"targetingType" => null,
						"relationshipType" => null,
						"targetingEntries" => []
					  ],
					  "desktop" => [
						"environmentId" => 3,
						"targetingType" => null,
						"relationshipType" => null,
						"targetingEntries" => []
					  ],
					  "ctv" => [
						"environmentId" => 4,
						"targetingType" => null,
						"relationshipType" => null,
						"targetingEntries" => []
					  ]
				]
			],
			"eligibleWithoutAdsTxt" => false,
			"adsTxtIgnorePid" => false,
			"playInitTargeting" => null
		],
		"adTag" => getAdTag($type, $demandTagUrl),
		"sslAdTag" => null,
		"supportsSsl" => 1,
		"requiredMacros" => [],
		"partnerId" => null,
		"partnerName" => null,
		"programmaticBuyerId" => null,
		"isProgrammaticBuyerPrivateDealTag" => getIsProgrammaticBuyerPrivateDealTag($type),
		"clickthroughUrl" => getClickthroughUrl($type, $clickThroughUrl),
		"hapticPlayerUrl" => null,
		"companionAssetClickthroughUrl" => null,
		"daypartStatus" => $status,
		"daypartTimezone" => $dealInfo['daypartTimezone'],
		"daypartEntries" => [],
		"tagSiteTargeting" => [
			"additions" => $additions,
			"deletions" => [],
			"updates" => [],
			"state" => "no-direct-changes"
		],
		"parentSiteApplicationType" => "all",
		"targetedParentSiteIds" => [],
		"verificationVendors" => [],
		"trackingPixels" => $pixels,
		"timeout" => "",
		"clientSideFirstLookTimeoutMs" => null,
		"adTagRequestMethod" => null,
		"prebidFilters" => [
			"whiteOps" => [
				"environmentChoice" => "disabled",
				"all" => false,
				"mobileWeb" => false,
				"mobileApp" => false,
				"desktop" => false,
				"ctv" => false
			],
			"pixalate" => [
				"environmentChoice" => "disabled",
				"all" => false,
				"mobileWeb" => false,
				"mobileApp" => false,
				"desktop" => false,
				"ctv" => false
			],
			"brightcom" => [
				"environmentChoice" => "default",
				"all" => false,
				"mobileWeb" => false,
				"mobileApp" => false,
				"desktop" => false,
				"ctv" => false
			],
			"doubleVerify" => [
				"environmentChoice" => "default",
				"all" => false,
				"mobileWeb" => false,
				"mobileApp" => false,
				"desktop" => false,
				"ctv" => false
			]
		],
		"weight" => getTagWeight($type),
		"lkqdAccountPublishersSelected" => [],
		"supplyTargetingType" => "open",
		"googleImaSdkSupport" => false,
		"startTs" => null,
		"endTs" => null,
		"customFlightTimeZone" => null
	];
}

/**
 * Creates a new demand tag in LKQD
 */
function newDemandTag(
	int $dealId,
	string $name,
	string $status,
	string $clickThroughUrl,
	string $trackingPixels,
	int $creativeId,
	string $environments,
	string $countries,
	int $type,
	string $demandTagUrl
) {
	global $cookie_file;

	$sources = getSources();
	if (UNAUTHORIZED_PREFIX === $sources) {
		logIn('newDemandTag function');
		$sources = getSources();
	}
	$envs = json_decode($environments, true);
	$geoTargetingData = getGeoTargetingData(json_decode($countries, true));
	$envsArray = getEnvironments($envs);
	$dealInfo = getDealInfo($dealId);
	if (in_array(UNAUTHORIZED_PREFIX, $dealInfo)) {
		logIn('newDemandTag function');
		$dealInfo = getDealInfo($dealId);
	}

	$filteredSources = array_filter($sources, function ($source) use ($envs) {
		return $source->cpmFloorDemand >= 0.2 && in_array($source->environmentId, $envs);
	});

	$additions = getAdditions($filteredSources);
	$pixels = getTrackingPixels($trackingPixels);
	
	$url = 'https://ui-api.lkqd.com/tags';

	$levels = [];
	for ($j = 1; $j <= 40; $j++) {
		$levels[] = [
			"levelNum" => $j,
			"targetingType" => null,
			"parameters" => []
		];
	}

	$payload = getDemandTagPayload(0,
		$dealInfo,
		$dealId,
		$name,
		$status,
		$envsArray,
		$geoTargetingData,
		$levels,
		$clickThroughUrl,
		$additions,
		$pixels,
		$type,
		$demandTagUrl
	);

	$payloadJson = json_encode($payload);

	$headers = [
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/',
		'LKQD-Api-Version: 88',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	];


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch);

	$response = json_decode($result, true);

	if (!empty($response['errors'])) {
		return $response['errors'];
	}

	$demandTagId = $response['data']['tagId'];

	$addAssociations = getAddAssociations($filteredSources, $demandTagId);

	$updateDbAssociationsUrl = "https://api.lkqd.com/supply-tags/update-db-associations";
	$updateDbAssociationsPayload = [
		"removeAssociations" => [],
		"addAssociations" => $addAssociations,
		"updateAssociations" => []
	];

	$updateDbAssociationsUrlJson = json_encode($updateDbAssociationsPayload);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $updateDbAssociationsUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $updateDbAssociationsUrlJson);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch);

	$tagAssociationsUrl = "https://api.lkqd.com/demand/creatives/tag-associations";
	$tagAssociationsPayload = [
		"adds" => [
			[
			  "tagId" => $demandTagId,
			  "creativeId" => $creativeId
			]
		  ],
		"removes" => []
	];

	$tagAssociationsPayloadJson = json_encode($tagAssociationsPayload);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $tagAssociationsUrl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $tagAssociationsPayloadJson);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch);

	return $demandTagId;
}

function newSupplyPartner($SPName){
	global $cookie_file;
	
	$URL = 'https://api.lkqd.com/supply/partners';

	$RequestPayload = array(
		"name" => "$SPName",
		"publisherContactName" => null,
		"publisherContactEmail" => null,
		"assignedUserId" => null,
		"metrics" => array(
			"OPPORTUNITIES",
			"IMPRESSIONS",
			"FILL_RATE",
			"EFFICIENCY",
			"CPM",
			"REVENUE",
			"CTR",
			"FIRST_QUARTILE_RATE",
			"MIDPOINT_RATE",
			"THIRD_QUARTILE_RATE",
			"VTR",
			"CLICKS",
			"FIRST_QUARTILES",
			"MIDPOINTS",
			"THIRD_QUARTILES",
			"COMPLETED_VIEWS"
		)
	);
	
	$RequestPayloadJson = json_encode($RequestPayload);
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $RequestPayloadJson);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch); 

	$Data = json_decode($result);
	
	//print_r($Data);
	
	if(array_key_exists('supplyPartnerId', $Data)){
		return $Data->supplyPartnerId;
	}else{
		return $Data->errorId;
	}
}


function newSupplySource($SName, $SPId, $Env = 1, $Rev = 40, $Loop = 12, $debug = false){
	global $cookie_file;
	//$debug = true;
	
	if($Env == 1){
		$environmentId = 3;
		//$URL = "https://api.lkqd.com/supply-tags/find-by-id?siteId=909242";
		$URL = "https://api.lkqd.com/supply-tags/find-by-id?siteId=1132867";
	}else{
		$environmentId = 1;
		//$URL = "https://api.lkqd.com/supply-tags/find-by-id?siteId=909244";
		$URL = "https://api.lkqd.com/supply-tags/find-by-id?siteId=1132868";
	}
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
		
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
	$result = curl_exec($ch);
	curl_close($ch); 
	
	$Data = json_decode($result);
	//print_r($Data);
	//exit(0);
	if(property_exists($Data, 'errorId')){
		return $Data->errorId;
	}
	
	$tagSiteAssociations = array();
	
	foreach($Data->associations as $D){
		if(property_exists($D, 'priority')){
			$tagSiteAssociations[] = array(
				'tagId' => $D->tagId,
				'priority' => $D->priority
			);
		}else{
			$tagSiteAssociations[] = array(
				'tagId' => $D->tagId,
				'priority' => null
			);
		}
	}
	
	$URL = 'https://api.lkqd.com/supply/sources';
	
	$LastU = date('Y-m-d\TH:i:s.') . rand(100,999) . 'Z';

	$RequestPayload = array(
		"domain" => null,
		"adType" => "video",
		"siteType" => "normal",
		"status" => "active",
		"relationship" => "owned",
		"cpmFloor" => 2,
		"siteCost" => $Rev,
		"siteCostType" => "rev",
		"maxVideoAdDurationSec" => null,
		"sessionMaxOpportunities" => null,
		"sessionMaxImpressions" => $Loop,
		"sessionMinIntervalMs" => null,
		"minVpsr" => null,
		"strictMode" => false,
		"enforcePlatformConnections" => false,
		"allowFloorsPassedIn" => false,
		"serverSideVpaidAllowMediafiles" => "agency",
		"passbackTagType" => null,
		"demandTargetingType" => "open",
		"prebidFilters" => array(
			"whiteOps" => array("enabled" => false),
			"pixalate" => array("enabled" => false),
			"brightcom" => array("enabled" => false),
			"doubleVerify" => array("enabled" => false)
		),
		"lastUpdatedAt" => "$LastU",
		"tagSiteAssociations" => $tagSiteAssociations,
		"vastEnabledDesktop" => null,
		"vastEnabledMobileWeb" => null,
		"vastEnabledMobileApp" => null,
		"vastEnabledCtv" => null,
		"vastVpaidEnabledDesktop" => null,
		"vastVpaidEnabledMobileWeb" => null,
		"vastVpaidEnabledMobileApp" => null,
		"execution" => array(),
		"audienceCharacteristics" => array(),
		"serverSideAdInsertion" => false,
		"serverSideAdInsertionVendor" => null,
		"precache" => false,
		"precacheSec" => null,
		"precachingMaxAgeSec" => null,
		"verifiedDate" => null,
		"declinedDate" => null,
		"declineComment" => null,
		"submittedForVerificationDate" => null,
		"verificationState" => "unverified",
		"_verificationState" => "unverified",
		"_execution" => array(),
		"_audienceCharacteristics" => array(),
		"partnerId" => $SPId,
		"environmentId" => $environmentId,
		"siteName" => "$SName",
		"cpmFloorDemand" => 1.5,
		"maxDesktopAsyncSlots" => null
	);
	
	//print_r($RequestPayload);
	
	$RequestPayloadJson = json_encode($RequestPayload);
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $RequestPayloadJson);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch); 

	$Data = json_decode($result);
	//print_r($Data);
	
	if(array_key_exists('siteId', $Data)){
		return $Data->siteId;
	}else{
		if($debug){
			return $result;
		}else{
			return false;
		}
	}
}


function updateSupplySource($sID, $Name = '', $Rev = 40, $Loop = 12, $debug = false){
	global $cookie_file;
	
	$URL = 'https://api.lkqd.com/supply/sources/' . $sID;
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    
	$result = curl_exec($ch);
	curl_close($ch); 
	
	$Data = json_decode($result, FALSE);
	//print_r($Data);
	//exit(0);
	if(property_exists($Data, 'errorId')){
		return $Data->errorId;
	}
	
	//if()
	$SPId = $Data->partnerId;
	$Env = $Data->environmentId;
	$cpmFloor = $Data->cpmFloor;
	$cpmFloorDemand = $Data->cpmFloorDemand;
	$siteCostType = $Data->siteCostType;
	//$siteCost = $Data->siteCost;
	if($Name != ''){
		$siteName = $Name;
	}else{
		$siteName = $Data->siteName;
	}
	
	$tagSiteAssociations = (object) $Data->tagSiteAssociations;
	//var_dump($tagSiteAssociations);

	$URL = 'https://api.lkqd.com/supply/sources/lkqd/platform-connection-url?siteId=' . $sID . '&envId=' . $Env;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    
	$result = curl_exec($ch);
	curl_close($ch); 
	
	$Data = json_decode($result);
	//print_r($Data);
	$lkqdPlatformConnection = $Data->lkqdPlatformConnection;
	//exit(0);

	
	
	$URL = 'https://api.lkqd.com/supply/sources';
	
	$LastU = date('Y-m-d\TH:i:s.') . rand(100,999) . 'Z';

	$RequestPayload = array(
		"siteIds" => array($sID),
		"domain" => null,
		"adType" => "video",
		"siteType" => "normal",
		"status" => "active",
		"relationship" => "owned",
		"cpmFloor" => $cpmFloor,
		"siteCost" => $Rev,
		"siteCostType" => "$siteCostType",
		"maxVideoAdDurationSec" => null,
		"sessionMaxOpportunities" => null,
		"sessionMaxImpressions" => $Loop,
		"sessionMinIntervalMs" => null,
		"minVpsr" => null,
		"strictMode" => false,
		"enforcePlatformConnections" => false,
		"allowFloorsPassedIn" => false,
		"serverSideVpaidAllowMediafiles" => "agency",
		"passbackTagType" => null,
		"demandTargetingType" => "open",
		"prebidFilters" => array(
			"whiteOps" => array("enabled" => false),
			"pixalate" => array("enabled" => false),
			"brightcom" => array("enabled" => false),
			"doubleVerify" => array("enabled" => false)
		),
		"lastUpdatedAt" => "$LastU",
		"tagSiteAssociations" => $tagSiteAssociations,
		"vastEnabledDesktop" => null,
		"vastEnabledMobileWeb" => null,
		"vastEnabledMobileApp" => null,
		"vastEnabledCtv" => null,
		"vastVpaidEnabledDesktop" => null,
		"vastVpaidEnabledMobileWeb" => null,
		"vastVpaidEnabledMobileApp" => null,
		"execution" => (object) array(),
		"audienceCharacteristics" => (object) array(),
		"serverSideAdInsertion" => false,
		"serverSideAdInsertionVendor" => null,
		"precache" => false,
		"precacheSec" => null,
		"precachingMaxAgeSec" => null,
		"verifiedDate" => null,
		"declinedDate" => null,
		"declineComment" => null,
		"submittedForVerificationDate" => null,
		"verificationState" => "unverified",
		"_verificationState" => "unverified",
		"_execution" => array(),
		"_audienceCharacteristics" => array(),
		"siteId" => $sID,
		"publisherId" => 430,
		"partnerId" => $SPId,
		"siteName" => "$siteName",
		"environmentId" => $Env,
		"cpmFloorDemand" => $cpmFloorDemand,
		"iabCategoryId" => null,
		"serverSideRequestTimeoutMs" => null,
		"maxDesktopAsyncSlots" => null,
		"considerSiteCostInEligibility" => true,
		"considerFeesInEligibility" => true,
		"clientSideRequestTimeoutMs" => null,
		"passbackTag" => null,
		"creativeGatingAllowNonRtb" => false,
		"publisherDirect" => false,
		"lkqdPlatformConnection" => "$lkqdPlatformConnection"
	);
	
	//print_r($RequestPayload);
	
	$RequestPayloadJson = json_encode($RequestPayload);
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $RequestPayloadJson);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch); 

	$Data = json_decode($result);
	//print_r($Data);
	
	
	if($debug){
		return $result;
	}else{
		return true;
	}
	
}

function specialUpdateSupplySource($sID, $NewFloor, $debug = false){
	global $cookie_file;
	
	$URL = 'https://api.lkqd.com/supply/sources/' . $sID;
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    
	$result = curl_exec($ch);
	curl_close($ch); 
	
	$Data = json_decode($result, FALSE);
	//print_r($Data);
	//exit(0);
	if(property_exists($Data, 'errorId')){
		return $Data->errorId;
	}
	
	//if()
	$SPId = $Data->partnerId;
	$Env = $Data->environmentId;
	$cpmFloor = $Data->cpmFloor;
	//$cpmFloorDemand = $Data->cpmFloorDemand;
	$cpmFloorDemand = $NewFloor;
	$siteCostType = $Data->siteCostType;
	$siteCost = $Data->siteCost;
	$sessionMaxImpressions = $Data->sessionMaxImpressions;
	$siteName = $Data->siteName;
	
	
	$tagSiteAssociations = (object) $Data->tagSiteAssociations;
	//var_dump($tagSiteAssociations);

	$URL = 'https://api.lkqd.com/supply/sources/lkqd/platform-connection-url?siteId=' . $sID . '&envId=' . $Env;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    
	$result = curl_exec($ch);
	curl_close($ch); 
	
	$Data = json_decode($result);
	//print_r($Data);
	$lkqdPlatformConnection = $Data->lkqdPlatformConnection;
	//exit(0);

	
	
	$URL = 'https://api.lkqd.com/supply/sources';
	
	$LastU = date('Y-m-d\TH:i:s.') . rand(100,999) . 'Z';

	$RequestPayload = array(
		"siteIds" => array($sID),
		"domain" => null,
		"adType" => "video",
		"siteType" => "normal",
		"status" => "active",
		"relationship" => "owned",
		"cpmFloor" => $cpmFloor,
		"siteCost" => $siteCost,
		"siteCostType" => "$siteCostType",
		"maxVideoAdDurationSec" => null,
		"sessionMaxOpportunities" => null,
		"sessionMaxImpressions" => $sessionMaxImpressions,
		"sessionMinIntervalMs" => null,
		"minVpsr" => null,
		"strictMode" => false,
		"enforcePlatformConnections" => false,
		"allowFloorsPassedIn" => false,
		"serverSideVpaidAllowMediafiles" => "agency",
		"passbackTagType" => null,
		"demandTargetingType" => "open",
		"prebidFilters" => array(
			"whiteOps" => array("enabled" => false),
			"pixalate" => array("enabled" => false),
			"brightcom" => array("enabled" => false),
			"doubleVerify" => array("enabled" => false)
		),
		"lastUpdatedAt" => "$LastU",
		"tagSiteAssociations" => $tagSiteAssociations,
		"vastEnabledDesktop" => null,
		"vastEnabledMobileWeb" => null,
		"vastEnabledMobileApp" => null,
		"vastEnabledCtv" => null,
		"vastVpaidEnabledDesktop" => null,
		"vastVpaidEnabledMobileWeb" => null,
		"vastVpaidEnabledMobileApp" => null,
		"execution" => (object) array(),
		"audienceCharacteristics" => (object) array(),
		"serverSideAdInsertion" => false,
		"serverSideAdInsertionVendor" => null,
		"precache" => false,
		"precacheSec" => null,
		"precachingMaxAgeSec" => null,
		"verifiedDate" => null,
		"declinedDate" => null,
		"declineComment" => null,
		"submittedForVerificationDate" => null,
		"verificationState" => "unverified",
		"_verificationState" => "unverified",
		"_execution" => array(),
		"_audienceCharacteristics" => array(),
		"siteId" => $sID,
		"publisherId" => 430,
		"partnerId" => $SPId,
		"siteName" => "$siteName",
		"environmentId" => $Env,
		"cpmFloorDemand" => $cpmFloorDemand,
		"iabCategoryId" => null,
		"serverSideRequestTimeoutMs" => null,
		"maxDesktopAsyncSlots" => null,
		"considerSiteCostInEligibility" => true,
		"considerFeesInEligibility" => true,
		"clientSideRequestTimeoutMs" => null,
		"passbackTag" => null,
		"creativeGatingAllowNonRtb" => false,
		"publisherDirect" => false,
		"lkqdPlatformConnection" => "$lkqdPlatformConnection"
	);
	
	//print_r($RequestPayload);
	
	$RequestPayloadJson = json_encode($RequestPayload);
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
	curl_setopt($ch, CURLOPT_POSTFIELDS, $RequestPayloadJson);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch); 
	
	$Data = json_decode($result);
	//print_r($Data);
	
	if($debug){
		return $Data;
	}else{
		return true;
	}
	
}

function getSupplySourceNameLoopRev($sID){
	global $cookie_file;
	
	$URL = 'https://api.lkqd.com/supply/sources/' . $sID;
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    
	$result = curl_exec($ch);
	curl_close($ch); 
	
	$Data = json_decode($result, FALSE);
	//print_r($Data);
	//exit(0);
	if(property_exists($Data, 'errorId')){
		return $Data->errorId;
	}else{
		$siteName = $Data->siteName;
		$siteCost = $Data->siteCost;
		$Loop = $Data->sessionMaxImpressions;
		
		return array('Name' => $siteName, 'Rev' => $siteCost, 'Loop' => $Loop);
	}
}

function getStatsPlusCountry($Date){ //TO TEST
	global $sessionId, $cookie_file, $post;
	
	$headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'LKQD-Api-Version: 88',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/reports/7712',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36'
	);
	
	$post = array(
		"timeDimension" => "DAILY",
		"reportType" => array("PARTNER", "SITE", "COUNTRY"),
		"reportFormat" => "JSON",
		//"metrics" => array("IMPRESSIONS","REVENUE","CLICKS","FORMAT_LOADS","COST","PROFIT","OPPORTUNITIES"),
		"startDate" => $Date,
		"endDate" => $Date,
		"timezone" => "America/New_York"
		//"limit" => 20
	);
	
	$json = json_encode($post);
	$url = 'https://ui-api.lkqd.com/reports';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$json);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
	$result = curl_exec($ch);
	curl_close($ch);  
	$deco = json_decode($result);
	
	return $deco;
}