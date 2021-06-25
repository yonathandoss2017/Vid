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
	
	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');
	
	for($N = 100; $N < 250; $N = $N + 5){
		echo $N . '<br/><br/><br/><br/><br/>';
		
		$sql = "SELECT * FROM reports_resume201911 WHERE idSite > 0 ORDER BY id ASC LIMIT " . $N . "0000, 50000";
		$query = $db->query($sql);
		
		if($db->num_rows($query) > 0){
			
			$Nins = 0;
			$Nis = 0;
			$Coma = "";
			$Values = "";
			
			while($Da = $db->fetch_array($query)){
				
				$Nins++;
				$Nis++;
				$ID = $Da['id'];
				$idUser = $Da['idUser'];
				$idTag = $Da['idTag'];
				$idSite = $Da['idSite'];
				$idDomain = $Da['Domain'];
				$idCountry = $Da['Country'];
				$Impressions = $Da['Impressions'];
			    $Opportunities = $Da['Opportunities'];
			    $formatLoads = $Da['formatLoads'];
			    $Revenue = $Da['Revenue'];
			    $Coste = $Da['Coste'];
			    $ExtraprimaP = $Da['ExtraprimaP'];
			    $Extraprima = $Da['Extraprima'];
			    $Clicks = $Da['Clicks'];
			    $Wins = $Da['Wins'];
			    $adStarts = $Da['adStarts'];
			    $FirstQuartiles = $Da['FirstQuartiles'];
			    $MidViews = $Da['MidViews'];
			    $ThirdQuartiles = $Da['ThirdQuartiles'];
			    $CompletedViews = $Da['CompletedViews'];
			    $Date = $Da['Date'];
			    
			    $timeAdded = 0;
			    $lastUpdate = 0;
			    
			    $Values .= "$Coma ('$ID', '$idUser', '$idTag', '$idDomain', '$idCountry', '$Impressions', '$Opportunities', '$Revenue', '$Coste', '$ExtraprimaP', '$Clicks', '$Wins',  '$adStarts', '$FirstQuartiles', '$Extraprima', '$MidViews', '$ThirdQuartiles', '$CompletedViews', '$timeAdded', '$lastUpdate', '$Date', '$idSite', '$formatLoads')";
				$Coma = ", ";
				
				if($Nins > 3000){
					$sql = "INSERT INTO reportsresume201911 (id, iduser, id_tag, domain, country, impressions, opportunities, revenue, coste, extra_prima_p, clicks, wins, ad_starts, first_quartiles, extraprima, mid_views, third_quartiles, completed_views, time_added, last_update, date, idsite, formatloads) VALUES $Values ;";			
					$db2->query($sql);
					$Nins = 0;
					$Values = "";
					$Coma = "";
					
					echo '====>INSERTA<==== ';
				}
				
				echo "$Nis ";
				
			}
			
			if($Nins > 1){
				$sql = "INSERT INTO reportsresume201911 (id, iduser, id_tag, domain, country, impressions, opportunities, revenue, coste, extra_prima_p, clicks, wins, ad_starts, first_quartiles, extraprima, mid_views, third_quartiles, completed_views, time_added, last_update, date, idsite, formatloads) VALUES $Values ;";			
				$db2->query($sql);
				
				echo "FINAL INSERT";
			}
		}
	}
	