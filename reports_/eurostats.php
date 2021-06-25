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
	//require('countries.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	mysqli_set_charset($db->link, 'utf8');
	
	for($I = 0; $I <= 20; $I++){
		$sql = "SELECT * FROM stats2 WHERE RevenueEuros = '0.00' AND Revenue > 0 ORDER BY id ASC LIMIT 100000";
		$query = $db->query($sql);
		while($R = $db->fetch_array($query)){
			$idS = $R['id'];
			
			$RevenueE = correctCurrency($R['Revenue'], 2);
			$CosteE = correctCurrency($R['Coste'], 2);
			
			$sql = "UPDATE stats2 SET CosteEuros = '$CosteE', RevenueEuros = '$RevenueE' WHERE id = '$idS' LIMIT 1";
			$db->query($sql);
			
			
		}
		echo $idS . "\n";
	}
	