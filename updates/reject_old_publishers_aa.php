<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	//require('countries.php');
	require('/var/www/html/login/admin/libs/display.lib.php');

	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	$sql = "SELECT * FROM users WHERE AccM = 9999 AND id <= 26614";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($U = $db->fetch_array($query)){
			$idUser = $U['id'];
			//$sql = "UPDATE users SET AccM = 9999 WHERE id = '$idUser' LIMIT 1";
			//echo $sql . "\n";
			//$db->query($sql);
			
			$sql = "UPDATE user SET status = 5 WHERE id = '$idUser' LIMIT 1";
			echo $sql . "\n";
			$db2->query($sql);
		}
	}
	
	//newGenerateJS(11834);