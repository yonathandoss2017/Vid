<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	require('libs/common.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	date_default_timezone_set('America/New_York');
	
	
	
	$idG = intval($_GET['idg']);
	
	$sql = "SELECT Active FROM demandgroup WHERE id = '$idG' LIMIT 1";
	if($db->getOne($sql) == 1){
		$NewActive = 0;
	}else{
		$NewActive = 1;
	}
	
	$sql = "UPDATE demandgroup SET Active = '$NewActive' WHERE id = '$idG' LIMIT 1";
	$db->query($sql);
	
	echo $NewActive;