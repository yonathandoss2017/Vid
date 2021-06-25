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
	
	
	

	//$dbpass2 = "ViDo0-PROD_2020";
	//$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	
	$dbpass2 = "vidooDev-Pass_2020";
	$dbhost2 = "publisher-panel-for-dev.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";

	$dbuser2 = "root";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$Countries = array();
	
	/*
	$sql = "SELECT id, country FROM reportsresume201912";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		$N = 0;
		while($S = $db2->fetch_array($query)){
			$N++;
			$idR = $S['id'];
			$country = $S['country'];
			
			if(array_key_exists($country, $Countries)){
				$idVidoomyC = $Countries[$country];
			}else{
				$sql = "SELECT idVidoomy FROM reports_country_names WHERE id = '$country' LIMIT 1";
				$idVidoomyC = $db->getOne($sql);
				$Countries[$country] = $idVidoomyC;
			}
			
			$sql = "UPDATE reportsresume201912 SET country = '$idVidoomyC' WHERE id = '$idR' LIMIT 1";
			
			$db2->query($sql);
			if($N >= 50000){
				echo $idR . '-';
				$N = 0;
			}
			//exit(0);
			
		}
	}
	
	*/
	
	$sql = "SELECT id, idcountry, usd_cost FROM stats_country_r2019";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		$N = 0;
		while($S = $db2->fetch_array($query)){
			$N++;
			$idR = $S['id'];
			$country = $S['idcountry'];
			$CosteEur = correctCurrency($S['usd_cost'], 2);
			
			if(array_key_exists($country, $Countries)){
				$idVidoomyC = $Countries[$country];
			}else{
				$sql = "SELECT idVidoomy FROM reports_country_names WHERE id = '$country' LIMIT 1";
				$idVidoomyC = $db->getOne($sql);
				$Countries[$country] = $idVidoomyC;
			}
			
			$sql = "UPDATE stats_country_r2019 SET idcountry = '$idVidoomyC', eur_cost = '$CosteEur' WHERE id = '$idR' LIMIT 1";
			$db2->query($sql);
			
			if($N >= 50000){
				echo $idR . '-';
				$N = 0;
			}
			//exit(0);
			
		}
	}