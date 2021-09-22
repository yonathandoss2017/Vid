<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/db.php');
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
		
	$Datem10 = date('Y-m-d', time() - 10 * 24 * 3600);
	$ArrayCurrency = array();

	$DateSRate = array();
	$CountryOld = array();
	
	//$sql = "DELETE FROM stats_country_r2020 WHERE (idUser = 170 OR idUser = 337) ";
	
	$sql = "SELECT 
		idUser, Date, Country,
	    SUM(Coste) AS Coste,
	    SUM(CosteEur) AS CosteEur,
	    Date 
	    FROM reports_resume202109 WHERE idUser in (3921, 27988, 3920) AND Coste > 0 
    GROUP BY idUser, Country, Date"; // AND Date BETWEEN '2020-04-01' AND '2020-04-12'
	//idUser = 4258 OR
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
			//exit(0);
			//$idStat = 0;
			
			echo " - Query Insert 1: " . date('H:i:s');
			echo $sql = "INSERT INTO stats_country2020 (iduser, idcountry, usd_cost, eur_cost, date, last_update) 
			VALUES 
			('$idUser', '$idCountry', '$Coste', '$CosteEur', '$Date', '$lastUpdate')";
			$db2->query($sql);
			echo $idStat2 = mysqli_insert_id($db->link);
		//	exit(0);
			echo " - Query Insert 2: " . date('H:i:s');
				
				
				$sql = "INSERT INTO stats_country_last (iduser, idcountry, usd_cost, eur_cost, last_update, date) 
				VALUES 
				('$idUser', '$idCountry', '$Coste', '$CosteEur', '$lastUpdate', '$Date')";
				
				$db2->query($sql);
				
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
