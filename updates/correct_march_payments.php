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
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$dbuser3 = "root";
	$dbpass3 = "vidooDev-Pass_2020";
	$dbhost3 = "publisher-panel-for-dev.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname3 = "vidoomy";
	$db3 = new SQL($dbhost3, $dbname3, $dbuser3, $dbpass3);


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