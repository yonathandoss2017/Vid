<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	require('libs/common.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$sql = "SELECT id, Date, Hour FROM reports201909 WHERE Date = '2019-09-09 00:00:00' ORDER BY id ASC";
	
	$Query = $db->query($sql);
	while($Row = $db->fetch_array($Query)){
		$idRow = $Row['id'];
		$Date = $Row['Date'];
		$Hour = $Row['Hour'];
		if(intval($Hour) <= 9){
			$Hour = '0' . $Hour;
		}
		$NewDate = str_replace('00:00:00', $Hour . ':00:00', $Date);
		$sql = "UPDATE reports201909 SET Date = '$NewDate' WHERE id = $idRow LIMIT 1";	
		//echo "\n";
		$db->query($sql);
		//exit(0);
	}