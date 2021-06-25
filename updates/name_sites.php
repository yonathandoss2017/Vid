<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');
	
	$sql = "SELECT id FROM website WHERE sitename = ''";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($S = $db2->fetch_array($query)){
			$idSite = $S['id'];
			
			$sql = "SELECT sitename FROM sites WHERE id = '$idSite' LIMIT 1";
			$SiteName = $db->getOne($sql);
			echo $SiteName . '<br/>';
			
			$sql = "UPDATE website SET sitename = '$SiteName' WHERE id = '$idSite' LIMIT 1";
			$db2->query($sql);
		}
	}
