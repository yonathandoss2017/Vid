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
	require('/var/www/html/login/admin/libs/display.lib.php');
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	$db3 = new SQL($pubDev01['host'], $pubDev01['db'], $pubDev01['user'], $pubDev01['pass']);
	
	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');


	
	$sql = "SELECT * FROM website WHERE similar_web > 0";
	$query = $db3->query($sql);
	if($db3->num_rows($query) > 0){
		while($W = $db3->fetch_array($query)){

			$idWeb = $W['id'];
			
			$SW = $W['similar_web'];
			$VPPM = $W['viewed_pages_per_month'];
			$TVPM = $W['total_visits_per_month'];
			$PPV = $W['pages_per_visit'];
			
			$sql = "UPDATE website SET similar_web = '$SW', viewed_pages_per_month = '$VPPM', total_visits_per_month = '$TVPM', pages_per_visit = '$PPV' WHERE id = '$idWeb' LIMIT 1";
			$db2->query($sql);
			
			echo $sql . "\n";
			
		}
	}
	
	
	
	
	