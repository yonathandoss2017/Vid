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

	$Date = date('Y-m-d', time() - 3600);
	
	$Results = getResultsDay($Date);
	
	if($Results === false){
		echo "Loggin in... \n\n";
		logIn();
		$Results = getResultsDay($Date);
	}
	
	//print_r($Results);
	$missingTags = array();
	
	foreach($Results->data->entries as $entry){
		$Impressions = $entry->adImpressions;
		$Opportunities = $entry->adOpportunities;
		$Revenue = $entry->revenue;
		$Coste = $entry->siteCost;
		$Clicks = $entry->adClicks;
		$LKQDid = $entry->fieldId;
		$LKQDuser = $entry->fieldName;
		$TagId = $entry->dimension2Id;
		$Tag = $entry->dimension2Name;
		$formatLoads = $entry->formatLoads;
		
		$timeAdded = time();
		$lastUpdate = time();
		
		$inserta = 0;
		
		$sql = "SELECT id FROM " . TAGS . " WHERE idTag = '$TagId' AND idPlatform = 1 ORDER BY id DESC LIMIT 1";
		$idTag = $db->getOne($sql);
		if($idTag > 0){
			$inserta = 1;	
			$sql = "SELECT idSite FROM " . TAGS . " WHERE id = '$idTag' LIMIT 1";
			$idSite = intval($db->getOne($sql));
			$sql = "SELECT idUser FROM " . TAGS . " WHERE id = '$idTag' LIMIT 1";
			$idUser = intval($db->getOne($sql));
		}else{
			$inserta = 0;
			
			if(!in_array($TagId, $missingTags)){
				$missingTags[] = $TagId;
				
				echo $TagId . ': ' . $Tag . "\n";
			}
		}
		
		if($inserta == 1){
			$sql = "SELECT id FROM stats WHERE idUser = '$idUser' AND idTag = '$idTag' AND Date = '$Date'";
			$idStat = $db->getOne($sql);
			if($idStat > 0){
				$sql = "UPDATE stats SET Impressions = '$Impressions', Opportunities = '$Opportunities', formatLoads = '$formatLoads', Revenue = '$Revenue', Coste = '$Coste', Clicks = '$Clicks', lastUpdate = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
				$db->query($sql);
			}else{
				$sql = "INSERT INTO stats (idUser, idTag, idSite, Impressions, Opportunities, formatLoads, Revenue, Coste, Clicks, timeAdded, lastUpdate, Date) VALUES ('$idUser', '$idTag', '$idSite', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$Clicks', '$timeAdded', '$lastUpdate', '$Date')";
				$db->query($sql);
			}
		}
	}
	
	echo 'OK 1';
	
	
	$dbuser2 = "root";
	$dbpass2 = "123123123";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$Day = $Date;
	
	$sql = "SELECT * FROM stats WHERE Date = '$Day'";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		
		$sql = "DELETE FROM stats WHERE date = '$Day'";
		$db2->query($sql);
		
		while($S = $db->fetch_array($query)){	
			
			$idS = $S['id'];
			$idUser = $S['idUser'];
			$idTag = $S['idTag'];
			$idSite = $S['idSite'];
			$Impressions = $S['Impressions'];
			$Opportunities = $S['Opportunities'];
			$formatLoads = $S['formatLoads'];
			$Revenue = $S['Revenue'];
			$Coste = $S['Coste'];
			
			$RevenueE = correctCurrency($S['Revenue'], 2);
			$CosteE = correctCurrency($S['Coste'], 2);
			
			$Clicks = $S['Clicks'];
			$timeAdded = $S['timeAdded'];
			$lastUpdate = $S['lastUpdate'];
			$Date = $S['Date'];
			
			$sql = "INSERT INTO stats (id, iduser, idtag, idsite, impressions, opportunities, format_loads, usd_revenue, eur_revenue, usd_cost, eur_cost, clicks, time_added, last_update, date)
				VALUES
				('$idS', '$idUser','$idTag','$idSite','$Impressions','$Opportunities','$formatLoads','$Revenue','$Coste','$RevenueE','$CosteE','$Clicks','$timeAdded','$lastUpdate','$Date')";
			$db2->query($sql);
			//break;
			
		}
	}
	
	echo ' OK 2';