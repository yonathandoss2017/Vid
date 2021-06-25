<?php 
	@session_start();
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('config.php');
	require('constantes.php'); 
	require('db.php');
	require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	
	
	$sql = "SELECT * FROM " . SITES . " WHERE id = 2214";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Site = $db->fetch_array($query)){
			generateJSDouble($Site['id']);
		}
	}
	