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
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	//$Date = date('Y-m-d', time() - 3600);
	
	$ADates = array(
		'2019-12-13',
		'2019-12-14',
		'2019-12-15',
		'2019-12-16',
		'2019-12-17',
		'2019-12-18',
		'2019-12-19',
		'2019-12-20',
		'2019-12-21',
		'2019-12-22',
		'2019-12-23',
		'2019-12-24',
		'2019-12-25',
		'2019-12-26',
		'2019-12-27',
		'2019-12-28',
		'2019-12-29',
		'2019-12-30',
		'2019-12-31',
		'2020-01-01',
		'2020-01-02',
		'2020-01-03',
		'2020-01-04',
		'2020-01-05',
		'2020-01-06',
		'2020-01-07',
		'2020-01-08',
		'2020-01-09'
	);
	
	foreach($ADates as $Day){
		
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
		
		echo $Day . ' OK 2';
	}