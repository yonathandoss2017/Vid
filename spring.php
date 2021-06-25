<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('config.php');
	require('constantes.php');
	require('db.php');
	require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$Date = date('Y-m-d', time() - 3600);
	//$Date = '2018-01-22';

	$headers = array('Content-Type:application/json');
	
	$post = array(
		"email" => "adops@vidoomy.com",
		"password" => "Vidoomymolamazo"
	);
	$json_encode = json_encode($post);
	
	$url = 'https://video.springserve.com/api/v0/auth';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$json_encode);
	$result = curl_exec($ch);
	curl_close($ch);  
	
	$json_decode = json_decode($result);
	//print_r($json_decode);
	//exit(0);
	$token = $json_decode->token;
	
	$headers = array(
	    "Content-Type:application/json",
	    "Authorization: $token"
	);
	
	$post = array(
		"start_date" => $Date,
		"end_date" => $Date,
		"interval" => "day",
		"dimensions" => array("supply_partner_id","supply_tag_id")
	);
	$json_encode = json_encode($post);
	
	$url = 'https://video.springserve.com/api/v0/report';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$json_encode);
	$result = curl_exec($ch);
	curl_close($ch);  
	
	$json_decode = json_decode($result);
	//print_r($json_decode);
	//exit(0);
	foreach($json_decode as $entry){
		$Impressions = $entry->total_impressions;
		$Opportunities = $entry->opportunities;
		$Revenue = $entry->revenue;
		$Coste = $entry->cost;
		$Clicks = $entry->clicks;
		$SSid = $entry->supply_partner_id;
		$SSuser = $entry->supply_partner_name;
		$TagId = $entry->supply_tag_id;
		$Tag = $entry->supply_tag_name;
		$timeAdded = time();
		$lastUpdate = time();
		
		$inserta = 0;
		
		$sql = "SELECT id FROM " . USERS . " WHERE SS_id = '$SSid' LIMIT 1";
		$idUser = $db->getOne($sql);
		if($idUser > 0){
			$inserta = 1;
		}else{
			$inserta = 0;
			/*
			$sql = "INSERT INTO " . USERS . " (user, password, email, SS_User, SS_id, lastlogin, time, date) 
			VALUES ('$SSuser', '-', '-', '$SSuser', '$SSid', '0', '$timeAdded', '$Date')";
			$db->query($sql);
			$idUser = mysqli_insert_id($db->link);
			*/
		}
		
		$sql = "SELECT id FROM " . TAGS . " WHERE idTag = '$TagId' AND idPlatform = 2 ORDER BY id DESC LIMIT 1";
		$idTag = $db->getOne($sql);
		if($idTag > 0){
			$inserta = 1;
			$sql = "SELECT idSite FROM " . TAGS . " WHERE id = '$idTag' LIMIT 1";
			$idSite = intval($db->getOne($sql));
		}else{
			$inserta = 0;
			/*
			$sql = "INSERT INTO " . TAGS . " (idUser, idTag, TagName) VALUES ($idUser, '$TagId', '$Tag')";
			$db->query($sql);
			$idTag = mysqli_insert_id($db->link);
			*/
		}
		
		if($inserta == 1){
			$sql = "SELECT id FROM " . STATS . " WHERE idUser = '$idUser' AND idTag = '$idTag' AND Date = '$Date'";
			$idStat = $db->getOne($sql);
			if($idStat > 0){
				$sql = "UPDATE " . STATS . " SET Impressions = '$Impressions', Opportunities = '$Opportunities', Revenue = '$Revenue', Coste = '$Coste', Clicks = '$Clicks', lastUpdate = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
				$db->query($sql);
			}else{
				$sql = "INSERT INTO " . STATS . " (idUser, idTag, idSite, Impressions, Opportunities, Revenue, Coste, Clicks, timeAdded, lastUpdate, Date) VALUES ('$idUser', '$idTag', '$idSite', '$Impressions', '$Opportunities', '$Revenue', '$Coste', '$Clicks', '$timeAdded', '$lastUpdate', '$Date')";
				$db->query($sql);
			}
		}
		
	}
	echo 'OK';
?>