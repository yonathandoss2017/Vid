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
	require('libs/display.lib.php');
	
	if(@$_SESSION['Type'] == 3){
		
		$sql = "SELECT * FROM " . SITES . " WHERE deleted = 0";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			while($Site = $db->fetch_array($query)){
				if($Site['filename'] != ''){
					$idSite = $Site['id'];
					newGenerateJS($idSite);
				}
			}
		}
		
	}