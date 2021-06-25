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
	$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	/*
	$dbuser3 = "root";
	$dbpass3 = "N6kdTJ66kFjNHByUU9tJW5V";
	$dbhost3 = "vidoomy-integration.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname3 = "staging";
	$db3 = new SQL($dbhost3, $dbname3, $dbuser3, $dbpass3);
	*/
	
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