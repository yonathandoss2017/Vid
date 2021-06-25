<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$Date = date('Y-m-d', time() - 3600);
	
	
	$headers = array(
	    'Content-Type:application/json',
	    'Authorization: Basic '. base64_encode("U0qJXH2r9FCaPdZBr1WXvN1TQdxoEX7D:2fJL9Fx1ft6mAEHbz0112RlCjvEJm_k1EObfVgTtbDc") // <---
	);
	$post = array(
		"name" => "Test 2",
		"allowHostNames" => false,
		"entries" => array(
			'adds' => array(
				'google.com',
				'facebook.com'
			),
			'removes' => array()
		)
	);
	
	
	
	
	echo $json_encode = json_encode($post);
	
	//exit(0);
	$url = 'https://api.lkqd.com/restrictions/domain-lists/435347';
	//$url = 'https://api.lkqd.com/restrictions/domain-lists';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$json_encode);
	$result = curl_exec($ch);
	curl_close($ch);  
	
	//$decoded_result = json_decode($result);
	print_r($result);
	
	//echo $url;
	
	exit(0);
