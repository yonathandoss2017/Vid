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
	
	$sql = "SELECT * FROM website_zone ORDER BY id ASC";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($Z = $db2->fetch_array($query)){
			$ID = $Z['id'];
			$ZID = $Z['zone_id'];
			
			$sql = "SELECT ad_type_id FROM ad WHERE lkqdid = '$ZID'";
			$AdType = intval($db2->getOne($sql));
			
			$sql = "UPDATE website_zone SET ad_type_id = $AdType WHERE id = $ID LIMIT 1";
			$db2->query($sql);
		}
	}