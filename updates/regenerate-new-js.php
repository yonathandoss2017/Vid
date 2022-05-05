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
	//require('countries.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	require('/var/www/html/login/admin/libs/display.lib.php');
	
	/*
	$sql = "SELECT id FROM sites WHERE deleted = 0";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($W = $db->fetch_array($query)){
			newGenerateJS($W['id']);
			echo $W['id'] . "\n";
		}
	}
	*/
	newGenerateJS(19695);
	//newGenerateJS(5805);