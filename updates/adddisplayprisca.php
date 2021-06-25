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
	
	$sql = "SELECT * FROM sites WHERE idUser = 64";
	
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Row = $db->fetch_array($query)){
			
			
			echo $Row['sitename'] . "\n";
			
			
		}
	}