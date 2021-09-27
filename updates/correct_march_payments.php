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
	$db3 = new SQL($pubDev01['host'], $pubDev01['db'], $pubDev01['user'], $pubDev01['pass']);


	$Rate = 1.1063;

	$sql = "SELECT * FROM payment WHERE month = 3 AND year = 2020";
	$query = $db2->query($sql);
	while($S = $db2->fetch_array($query)){
		$idS = $S['id'];
		$Amount = $S['usdamount'];
		$EurA = number_format($Amount / $Rate, 3);
		
		$sql = "UPDATE payment SET euramount = '$EurA' WHERE id = '$idS' LIMIT 1";
		echo "$sql \n";
		$db2->query($sql);

	}
	
	$sql = "SELECT * FROM closure WHERE created_at >= '2020-04-14 00:00:00'";
	$query = $db2->query($sql);
	while($S = $db2->fetch_array($query)){
		$idS = $S['id'];
		$Amount = $S['usdamount'];
		$EurA = number_format($Amount / $Rate, 2);
		
		$sql = "UPDATE closure SET euramount = '$EurA' WHERE id = '$idS' LIMIT 1";
		echo "$sql \n";
		$db2->query($sql);

	}