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
	//require('countries.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	mysqli_set_charset($db->link, 'utf8');
	
	$sql = "SELECT id, sitename FROM sites WHERE id = '636' OR id = '1056' OR id = '90' OR id = '1171' OR id = '3735' OR id = '1055' OR id = '300' OR id = '634' OR id = '3734' OR id = '635' ";
	$query = $db->query($sql);
	while($S = $db->fetch_array($query)){
		$ArraySites[$S['id']] = $S['sitename'];
	}
	
	
	echo serialize($ArraySites);
	
	
