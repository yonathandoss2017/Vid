<?php	
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
	require('/var/www/html/login/admin/lkqdimport/common.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	//exit(0);
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie.txt';

	$DateTo = '2019-10-30';
	$DateFrom = '2019-08-01';
	
	$HFrom = 0;
	$HTo = 23;
	
	logIn();
	
	$uuid = gen_uuid();
	$fileDownloadToken = rand(100000,999999);
	
	$post = array(
		"whatRequest" => "breakdown",
		"uuid" => $uuid,
		"reportFormat" => "JSON",
		"includeSummary" => "false",
		"dateRangeType" => "CUSTOM",
		"startDate" => $DateFrom,
		"endDate" => $DateTo,
		"startDateHour" => $HFrom,
		"endDateHour" => $HTo,
		"startHour" => 0,
		"endHour" => 23,
		"timeDimension" => "OVERALL",
		"timezone" => "America/New_York",
		"reportType" => array("PARTNER", "DOMAIN"),
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
			"FORMAT_LOADS"
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
	
	file_put_contents ( '../../rep.json' , $result );
	
	echo $result;