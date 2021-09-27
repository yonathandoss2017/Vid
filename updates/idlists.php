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
	$db3 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);

	$IDS = '';

	$sql = "SELECT * FROM user WHERE roles LIKE '%a:1:{i:0;s:14%'";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($U = $db2->fetch_array($query)){
			$idU = $U['id'];
			
			$sql = "SELECT enable_new FROM users WHERE id = $idU LIMIT 1";
			if($db->getOne($sql) == 0){
				echo $idU . "\n";
				$IDS .= $idU . "\n";
			}
		}
	}
	
	file_put_contents('users_to_notify.txt', $IDS);