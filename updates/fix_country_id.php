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
	
	$dbuser2 = "root";
	$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$dbuser4 = "root";
	$dbpass4 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost4 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname4 = "vidoomy";
	$db4 = new SQL($dbhost4, $dbname4, $dbuser4, $dbpass4);
	
	$CountryOld = array();
	$N = 0;
	
	$sql = "SELECT id, idcountry FROM stats_country2019 WHERE iduser = 26098 ORDER BY id ASC";
	$query = $db4->query($sql);
	$TotalRows = $db4->num_rows($query);
	if($TotalRows > 0){
		while($Da = $db4->fetch_array($query)){
			$N++;
			$idRow = $Da['id'];
			$idCountryOld = $Da['idcountry'];
			
			if(array_key_exists($idCountryOld, $CountryOld)){
				$idCountry = $CountryOld[$idCountryOld];
			}else{
				$sql = "SELECT idVidoomy FROM reports_country_names WHERE id = $idCountryOld LIMIT 1";
				$idCountry =$db->getOne($sql);
				
				$CountryOld[$idCountryOld] = $idCountry;
			}
			/*
			$sql = "UPDATE stats_country_last SET idcountry = $idCountry WHERE id = $idRow LIMIT 1;";
			$db2->query($sql);
			$db3->query($sql);
			*/
			$sql = "UPDATE stats_country2019 SET idcountry = $idCountry WHERE id = $idRow LIMIT 1;";
			$db2->query($sql);
			//$db3->query($sql);
			
			echo "$N / $TotalRows ID ROW: $idRow \n";
		}
	}