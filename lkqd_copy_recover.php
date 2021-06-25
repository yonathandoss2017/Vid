<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('config.php');
	require('constantes.php');
	require('db.php');
	require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$Date = date('Y-m-d', time() - (3600 * 1));
	$MonthT = date('Ym', time() - (3600 * 1));
	
	//exit(0);
	$Date = '2020-04-03';
	
	$sql = "SELECT 
	idUser, idSite, idTag,
	SUM(Coste) AS Coste,
	SUM(Revenue) AS Revenue,
	SUM(formatLoads) AS formatLoads,
	SUM(Impressions) AS Impressions,
	SUM(Opportunities) AS Opportunities,
	SUM(Clicks) AS Clicks
	
	FROM reports_resume$MonthT WHERE Date = '$Date' AND idUser > 0  AND idTag >= 13396 AND idTag <= 13401 GROUP BY idTag";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($S = $db->fetch_array($query)){
	
			$Impressions = $S['Impressions'];
			$Opportunities = $S['Opportunities'];
			$Revenue = $S['Revenue'];
			$Coste = $S['Coste'];
			$Clicks = $S['Clicks'];
			//$LKQDid = $entry->fieldId;
			//$LKQDuser = $entry->fieldName;
			//$TagId = $S['idTag'];
			//$Tag = $entry->dimension2Name;
			$formatLoads = $S['formatLoads'];
			
			$idSite = $S['idSite'];
			$idUser = $S['idUser'];
			$idTag = $S['idTag'];
			
			$timeAdded = time();
			$lastUpdate = time();
			
			$inserta = 1;
			
			if($inserta == 1){
				
				/*
				if($idSite == 11513 && date('d', time() - 3600) == 27){
					$Impressions = $formatLoads / 10.8;
					$Coste = $Impressions * 0.0015;
					$Revenue = $Coste * 1.4286;
				}
				*/
				/*
				if($idSite == 11630){
					$Impressions = $formatLoads / 4;
					$Coste = $Impressions * 0.0015;
					$Revenue = $Coste * 1.4286;
				}
				*/
				
				$sql = "SELECT id FROM " . STATS . " WHERE idUser = '$idUser' AND idTag = '$idTag' AND Date = '$Date' AND Manual = 0";
				$idStat = $db->getOne($sql);
				if($idStat > 0){
					$sql = "UPDATE " . STATS . " SET Impressions = '$Impressions', Opportunities = '$Opportunities', formatLoads = '$formatLoads', Revenue = '$Revenue', Coste = '$Coste', Clicks = '$Clicks', lastUpdate = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
					$db->query($sql);
				}else{
					$sql = "INSERT INTO " . STATS . " (idUser, idTag, idSite, Impressions, Opportunities, formatLoads, Revenue, Coste, Clicks, timeAdded, lastUpdate, Date, Manual) VALUES ('$idUser', '$idTag', '$idSite', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$Clicks', '$timeAdded', '$lastUpdate', '$Date', 0)";
					$db->query($sql);
				}
				//echo $sql . "\n";
				//exit(0);
			}else{
				$sql = "SELECT id FROM stats_missing WHERE idTag = '$TagId' AND Date = '$Date'";
				$idStat = $db->getOne($sql);
				if($idStat > 0){			
					$sql = "UPDATE stats_missing SET Impressions = '$Impressions', formatLoads = '$formatLoads', Revenue = '$Revenue' WHERE id = '$idStat' LIMIT 1";
					$db->query($sql);
				}else{
					$sql = "INSERT INTO stats_missing (idTag, formatLoads, Impressions, Revenue, TagName, Date, Time) 
					VALUES ('$TagId', '$formatLoads', '$Impressions', '$Revenue', '$Tag', '$Date', '$timeAdded')";
					$db->query($sql);
				}
			}
		}
	}
	echo 'OK 1';

	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
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
				('$idS', '$idUser','$idTag','$idSite','$Impressions','$Opportunities','$formatLoads','$Revenue','$RevenueE','$Coste','$CosteE','$Clicks','$timeAdded','$lastUpdate','$Date')";
			$db2->query($sql);
			//break;
			
		}
	}
	
	echo ' OK 2';
	
	
	$dbuser3 = "root";
	$dbpass3 = "vidooDev-Pass_2020";
	$dbhost3 = "publisher-panel-for-dev.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname3 = "vidoomy";
	$db3 = new SQL($dbhost3, $dbname3, $dbuser3, $dbpass3);
	
	$Day = $Date;
	
	$sql = "SELECT * FROM stats WHERE Date = '$Day'";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		
		$sql = "DELETE FROM stats WHERE date = '$Day'";
		$db3->query($sql);
		
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
				('$idS', '$idUser','$idTag','$idSite','$Impressions','$Opportunities','$formatLoads','$Revenue','$RevenueE','$Coste','$CosteE','$Clicks','$timeAdded','$lastUpdate','$Date')";
			$db3->query($sql);
			//break;
			
		}
	}
	
	echo ' OK 3';