<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../../config.php');
	require('../../constantes.php');
	require('../../db.php');
	require('../../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$WL = array();
	
	/*
	
		Open: tag id 906815
		WL: 999380 / WL: 435350
		TopWL: 999381 / WL 435351	
		
	*/
	
	$Date = date('Y-m-d', time() - 14200);
	
	//ANALIZA OPEN LIST
	echo $sql = "SELECT * FROM demandreport WHERE DemandTagID = '906815' AND Date = '$Date'";
	echo "\n";
	$Query = $db->query($sql);
	while($Row = $db->fetch_array($Query)){
		if($Row['Fill'] > 0.9){
			//ADD  WL 435350
			$WL[435350]['add'][] = $Row['Domain'];
		}
		if($Row['Fill'] > 2){
			//ADD TOP WL 435351
			$WL[435351]['add'][] = $Row['Domain'];
		}
	}
	
	//ANALIZA WHITE LIST
	echo $sql = "SELECT * FROM demandreport WHERE DemandTagID = '999380' AND Date = '$Date'";
	echo "\n";
	$Query = $db->query($sql);
	while($Row = $db->fetch_array($Query)){
		if($Row['Fill'] > 1.5){
			//ADD TOP WL 435351
			$WL[435351]['add'][] = $Row['Domain'];
		}
		if($Row['Requests'] >= 500){
			if($Row['Fill'] < 0.5){
				//REMOVE WL 435350
				$WL[435350]['remove'][] = $Row['Domain'];
			}
		}
	}
	
	//ANALIZA TOP WHITE LIST
	echo $sql = "SELECT * FROM demandreport WHERE DemandTagID = '999381' AND Date = '$Date'";
	echo "\n";
	$Query = $db->query($sql);
	while($Row = $db->fetch_array($Query)){
		if($Row['Requests'] >= 500){
			if($Row['Fill'] < 0.5){
				//REMOVE TOP WL 435351
				$WL[435351]['remove'][] = $Row['Domain'];
			}
		}
	}
	
	print_r($WL);
	//exit(0);
	/*


	foreach($WL as $WLId => $Domains){
		//print_r($Domains);
		
		$headers = array(
		    'Content-Type:application/json',
		    'Authorization: Basic '. base64_encode("U0qJXH2r9FCaPdZBr1WXvN1TQdxoEX7D:2fJL9Fx1ft6mAEHbz0112RlCjvEJm_k1EObfVgTtbDc") // <---
		);
		$post = array(
			"entries" => array(
				'adds' => $Domains['add'],
				'removes' => $Domains['remove']
			)
		);
		
		$json_encode = json_encode($post);
		
		//exit(0);
		$url = 'https://api.lkqd.com/restrictions/domain-lists/' . $WLId;
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
		
	}*/
	exit(0);
