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

	$Countries = array();
	
	$sql = "SELECT idUser, idCountry, Date, SUM(Coste) AS Coste FROM stats_country2018 WHERE Coste > 0 GROUP BY idUser, idCountry, Date";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($S = $db->fetch_array($query)){
			$idUser = $S['idUser'];
			$idCountry = $S['idCountry'];
			$Date = $S['Date'];
			$Coste = $S['Coste'];
			$CosteEur = correctCurrency($Coste, 2);
			
			if(array_key_exists($idCountry, $Countries)){
				$idVidoomyC = $Countries[$idCountry];
			}else{
				$sql = "SELECT idVidoomy FROM reports_country_names WHERE id = '$idCountry' LIMIT 1";
				$idVidoomyC = $db->getOne($sql);
				$Countries[$idCountry] = $idVidoomyC;
			}
			
			$sql = "INSERT INTO stats_country_r2018 (idUser, idCountry, Coste, CosteEur, Date) VALUES ('$idUser', '$idVidoomyC', '$Coste', '$CosteEur', '$Date')";
			$db->query($sql);
			//echo $sql;
			//exit(0);
		}
	}