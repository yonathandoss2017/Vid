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
	
	$Date = date('Y-m-d', time() - 6400);
	$MonD = date('Ym', time() - 6400);
	//$Date = '2020-02-19';

	$sql = "SELECT idTag, idSite, idUser,
	
	SUM(Revenue) AS Revenue, SUM(Coste) AS Coste, SUM(Impressions) AS Impressions, SUM(formatLoads) AS formatLoads, SUM(Clicks) AS Clicks
	
	FROM `reports$MonD` WHERE Manual = 3 AND Date = '$Date'
	GROUP BY idTag";
	
	
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		
		while($S = $db->fetch_array($query)){	
			$idUser = $S['idUser'];
			$idTag = $S['idTag'];
			$idSite = $S['idSite'];
			
			$Impressions = $S['Impressions'];
			$Opportunities = 0;
			$formatLoads = $S['formatLoads'];
			$Revenue = $S['Revenue'];
			$Coste = $S['Coste'];
			$Clicks = $S['Clicks'];
			$Impressions = $S['Impressions'];
	
			$sql = "SELECT id FROM " . STATS . " WHERE idUser = '$idUser' AND idTag = '$idTag' AND Date = '$Date' AND Manual = 3";
			$idStat = $db->getOne($sql);
			if($idStat > 0){
				$lastUpdate = time();
				
				$sql = "UPDATE " . STATS . " SET Impressions = '$Impressions', Opportunities = '$Opportunities', formatLoads = '$formatLoads', Revenue = '$Revenue', Coste = '$Coste', Clicks = '$Clicks', lastUpdate = '$lastUpdate' WHERE id = '$idStat' LIMIT 1";
				$db->query($sql);
				//echo $sql;
			}else{
				$timeAdded = time();
				$lastUpdate = time();
				
				$sql = "INSERT INTO " . STATS . " (idUser, idTag, idSite, Impressions, Opportunities, formatLoads, Revenue, Coste, Clicks, timeAdded, lastUpdate, Date, Manual) 
				VALUES ('$idUser', '$idTag', '$idSite', '$Impressions', '$Opportunities', '$formatLoads', '$Revenue', '$Coste', '$Clicks', '$timeAdded', '$lastUpdate', '$Date', 3)";
				$db->query($sql);
				//echo $sql;
			}
			
		}
	}