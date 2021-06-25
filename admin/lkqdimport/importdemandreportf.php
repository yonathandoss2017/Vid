<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../../config.php');
	require('../../constantes.php');
	require('../../db.php');
	require('../../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$DateF = date('Y-m-d', time() - 3600);
	//$DateF = date('Y-m-d', time() - 3600);
	$DateT = date('Y-m-d', time() - 3600);
	
	//$Date = '2018-01-26'  ;
	$sql = "TRUNCATE demandreport";
	$db->query($sql);
	
	
	
	$headers = array(
	    'Content-Type:application/json',
	    'Authorization: Basic '. base64_encode("U0qJXH2r9FCaPdZBr1WXvN1TQdxoEX7D:2fJL9Fx1ft6mAEHbz0112RlCjvEJm_k1EObfVgTtbDc") // <---
	);
	$post = array(
		"dateRangeType" => "CUSTOM",
		"timeDimension" => "DAILY",
		"reportType" => array("TAG","DOMAIN","COUNTRY"),
		"reportFormat" => "JSON",
		"metrics" => array("REQUESTS","IMPRESSIONS"),
		"startDate" => $DateF,
		"endDate" => $DateT,
		"whatRequest" => "breakdown",
		"timezone" => "America/New_York"
	);
	
	$json_encode = json_encode($post);
	//exit(0);

	$url = 'https://api.lkqd.com/reports';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$json_encode);
	$result = curl_exec($ch);
	curl_close($ch); 
	
	$myFile = 'json';
	$fh = fopen($myFile, 'w') or die("can't open");
	fwrite($fh, $result);
	fclose($fh);
	
	
	
	//$result = file_get_contents('json');
	$decoded_result = json_decode($result);
	
	
	//print_r($decoded_result);
	echo '123';
	//exit(0);
	
	/*
	$jsonTest = file_get_contents('json.test');
	
	$decoded_result = json_decode($jsonTest);
	*/
	
	foreach($decoded_result->data->entries as $entry){
		$Impressions = $entry->adImpressions;
		$Requests = $entry->adRequests;
		$TagId = $entry->fieldId;
		$CountryCode = $entry->dimension3Id;
		$Domain = $entry->dimension2Id;
		$Date = $entry->timeDimension;
		$Fill = $Impressions / $Requests * 100;
		
		
		$sql = "INSERT INTO demandreport (DemandTagID, Domain, Country, Requests, Impressions, Fill, Date) VALUES ('$TagId', '$Domain', '$CountryCode', '$Requests', '$Impressions', '$Fill', '$Date');";
		$db->query($sql);
		
	}
	echo 'OK';