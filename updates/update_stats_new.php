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
	
	$dbuser2 = "root";
	$dbpass2 = "123123123";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	//$Day = date('Y-m-d', time() - (24 * 3600));
	$Day = date('2019-11-04');
	
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
	

/*
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `idtag` int(11) NOT NULL,
  `idsite` int(11) DEFAULT NULL,
  `impressions` int(11) NOT NULL,
  `opportunities` int(11) NOT NULL,
  `format_loads` int(11) NOT NULL DEFAULT '0',
  `usd_revenue` double(6,2) NOT NULL,
  `eur_revenue` double(6,2) NOT NULL,
  `usd_cost` double(6,3) DEFAULT NULL,
  `eur_cost` double(6,3) DEFAULT NULL,
  `clicks` int(11) NOT NULL,
  `time_added` int(11) NOT NULL,
  `last_update` int(11) NOT NULL,
  `date` date NOT NULL,
 
*/
	echo 2;