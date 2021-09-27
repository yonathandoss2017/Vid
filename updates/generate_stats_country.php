<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');

	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
		
	$Datem10 = date('Y-m-d', time() - 10 * 24 * 3600);
	$ArrayCurrency = array();

	$DateSRate = array();
	$CountryOld = array();
	
	$sql = "DELETE FROM stats_country_r2020 WHERE Date >= '2021-05-13' ";
	$db->query($sql);
	
	$sql = "DELETE FROM stats_country2020 WHERE date >= '2021-05-13' ";
	$db2->query($sql);
	
	$sql = "SELECT 
		idUser, Date, Country,
	    SUM(Coste) AS Coste,
	    SUM(CosteEur) AS CosteEur,
	    Date 
	    FROM reports_resume202105 WHERE Coste > 0 AND Date >= '2021-05-13'
    GROUP BY idUser, Country, Date"; // AND Date BETWEEN '2020-04-01' AND '2020-04-12'
	
	$N = 0;
	$query = $db->query($sql);
	$TotalRows = $db->num_rows($query);
	if($TotalRows > 0){
		while($Da = $db->fetch_array($query)){
			$N++;
			$idUser = $Da['idUser'];
			$idCountryOld = $Da['Country'];
		    $Coste = $Da['Coste'];
		    $CosteEur = $Da['CosteEur'];
		    $Date = $Da['Date'];
		    
		    $timeAdded = time();
			$lastUpdate = time();
						
			if(array_key_exists($idCountryOld, $CountryOld)){
				$idCountry = $CountryOld[$idCountryOld];
			}else{
				$sql = "SELECT idVidoomy FROM reports_country_names WHERE id = $idCountryOld LIMIT 1";
				$idCountry =$db->getOne($sql);
				
				$CountryOld[$idCountryOld] = $idCountry;
			}
			

			$sql = "INSERT INTO stats_country_r2020 (idUser, idCountry, Coste, CosteEur, Date) VALUES ('$idUser', '$idCountry',  '$Coste', '$CosteEur', '$Date')";
			$db->query($sql);
			//$idStat = mysqli_insert_id($db->link);
			
			echo " - Query Insert 1: " . date('H:i:s');
			$sql = "INSERT INTO stats_country2020 (iduser, idcountry, usd_cost, eur_cost, date, last_update) 
			VALUES 
			('$idUser', '$idCountry', '$Coste', '$CosteEur', '$Date', '$lastUpdate')";
			$db2->query($sql);
			echo " - Query Insert 2: " . date('H:i:s');
				
				/*
				$sql = "INSERT INTO stats_country_last (id, iduser, idcountry, usd_cost, eur_cost, last_update, date) 
				VALUES 
				('$idStat', '$idUser', '$idCountry', '$Coste', '$CosteEur', '$lastUpdate', '$Date')";
				*/
				//$db2->query($sql);
				
			//}
			
			/*
			if($N >= 9999){
				echo "Still alive \n";
				$N = 0;
			}
			*/
			//echo $Date . '|';
		}
	}else{
		echo "No data found";
	}
