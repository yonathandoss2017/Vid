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
	
	$SumImp = 0;
	$SumImpPlus = 0;
	
	$sql = "SELECT * FROM basta ";
	$query = $db->query($sql);
	
	if($db->num_rows($query) > 0){
		
		while($Row = $db->fetch_array($query)){
						
			$Data = json_decode($Row['Content']);
			
			if(!property_exists($Data, 'email')){
				
				print_r($Data);
				exit(0);
				
			}
			
			
			
			
			
		}
	}
	
	