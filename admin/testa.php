<?php 
	@session_start();
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php'); 
	require('../db.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	
	
	$sql = "SELECT * FROM " . SITES . " WHERE id != 373 AND id != 1154 AND id != 931 AND id != 997 AND id != 2110 AND id != 564 AND id != 479 AND id != 663 AND id != 2126 AND id != 998 ";// 
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Site = $db->fetch_array($query)){
			generateJSDouble($Site['id'], true);
		}
	}
	