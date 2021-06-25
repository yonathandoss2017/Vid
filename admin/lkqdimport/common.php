<?php
	
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
	global $sessionId, $cookie_file;
	
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
	
	$post = array(
		//"userId" => "Vidoomy_Eric",
		//"password" => 'Vidoomy_Guau2020'
		//"password" => 'pitonaS29'
		//"password" => 'VidooPass_2021'
		//"password" => 'VidoomyPassword_202$'
		//"password" => 'jnsjJHs7ha_8jas$'
		//"password" => 'LKQDVidoomy2021%'
		
		//"userId" => "vidoomy_raquel",
		//"password" => 'Z,D>zQdsb48'
		"userId" => "Fede_Vidoomy",
		"password" => 'Hola222+++'
	);
	
	$json_encode = json_encode($post);
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
	
	$demandPartner = array_filter($data, function ($partner) use ($demandPartnerName) {
		return $partner["sourceName"] === $demandPartnerName;
	});

	if ($demandPartner) {
		return array_column($demandPartner, "sourceId")[0];
	}else{
		return "demand-partner-not-found";
	}
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

function newOrder(string $sourceId, string $name) {
	global $cookie_file;

	$URL = 'https://ui-api.lkqd.com/orders';

	$payload = [
		"orderId" => null,
		"orderName" => $name,
		"sourceId" => $sourceId,
		"thirdPartyOrderId" => null,
	];

	$payloadJson = json_encode($payload);
	
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
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch);
	return $result;

	return json_decode($result);
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

function newOrUpdateDeal(
	int $orderId,
	string $name,
	string $startDate,
	string $endDate,
	string $deliveryPacing,
	int $goal,
	string $status,
	int $dealId = null
) {
	global $cookie_file;

	$URL = 'https://ui-api.lkqd.com/deals';

	$deliveryPacings = [
		"1" => 3,
		"2" => 8,
	];

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
		"frequencyCaps" => [],
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
		"frequencyCapKey" => null,
		"frequencyCapNoUid" => "allowed",
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
		return $response->errors;
	}

	return $response->data->dealId;
}

function unselectSupplyPartner(array $tags) {
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
		return $data->errorId;
	}

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

	return json_decode($result, false);
}

function newDemandTag(int $dealId, string $name, string $status) {
	global $cookie_file;

	$sources = getSources();
	$filteredSources = array_filter($sources, function ($source) {
		return $source->cpmFloorDemand >= 0.2;
	});
	
	$url = 'https://ui-api.lkqd.com/tags';

	$levels = [];
	for ($j = 1; $j <= 40; $j++) {
		$levels[] = [
			"levelNum" => $j,
			"targetingType" => null,
			"parameters" => []
		];
	}

	$payload = [
		"tagId" => null,
		"dealCpm" => 0,
		"dealCpmType" => "fixed",
		"dealId" => $dealId,
		"dealTier" => 1,
		"dealStatus" => "complete",
		"name" => $name,
		"status" => $status,
		"tagType" => "lkqd-hosted",
		"tagSource" => "lkqd",
		"caps" => [],
		"frequencyCaps" => [],
		"frequencyCapKey" => null,
		"frequencyCapNoUid" => "allowed",
		"environments" => [
			[ "id" => 2 ],
			[ "id" => 3]
		],
		"adType" => "video",
  		"adDeliveryType" => "guaranteed",
		"targeting" => [
			"deviceOses" => [],
			"browserTargetingEntries" => [],
			"connectionTypeTargetingEntries" => [],
			"behavioralTargeting" => [
				"relationshipTypeBetweenProfiles" => null,
				"profiles" => []
			],
			"playerFormatTargetingEntries" => [],
			"geoTargetingData" => null,
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
				"method" => "detected",
				"allowUndetectable" => 1,
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
		"adTag" => null,
		"sslAdTag" => null,
		"supportsSsl" => 1,
		"requiredMacros" => [],
		"partnerId" => null,
		"partnerName" => null,
		"programmaticBuyerId" => null,
		"isProgrammaticBuyerPrivateDealTag" => null,
		"clickthroughUrl" => null,
		"hapticPlayerUrl" => null,
		"companionAssetClickthroughUrl" => null,
		"daypartStatus" => $status,
		"daypartTimezone" => "America/New_York",
		"daypartEntries" => [],
		"tagSiteTargeting" => [
			"additions" => [],
			"deletions" => [],
			"updates" => [],
			"state" => "no-direct-changes"
		],
		"parentSiteApplicationType" => "all",
		"targetedParentSiteIds" => [],
		"verificationVendors" => [],
		"trackingPixels" => [
			[
				"eventId" => 3,
				"pixelUrl" => "http =>//adimpression",
				"supplyScope" => null,
				"pixelId" => null
			],
			[
				"eventId" => 7,
				"pixelUrl" => "http =>//adcomplete",
				"supplyScope" => null,
				"pixelId" => null
			],
			[
				"eventId" => 8,
				"pixelUrl" => "http =>//adclick",
				"supplyScope" => null,
				"pixelId" => null
			]
		],
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
		"weight" => 100,
		"lkqdAccountPublishersSelected" => [],
		"supplyTargetingType" => "open",
		"googleImaSdkSupport" => false,
		"startTs" => null,
		"endTs" => null,
		"customFlightTimeZone" => null
	];

	$updateDbAssociationsUrl = "https://api.lkqd.com/supply-tags/update-db-associations";
	$updateDbAssociationsPayload = [
		"removeAssociations" => [],
		"addAssociations" => [],
		"updateAssociations" => []
	];

	$tagAssociationsUrl = "https://api.lkqd.com/demand/creatives/tag-associations";
	$tagAssociations = [
		"adds" => [
			[
			  "tagId" => 1055522,
			  "creativeId" => 28284
			]
		  ],
		"removes" => []
	];
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