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
	require('libs/display.lib.php');
		
	if(@$_SESSION['Admin'] == 1){
		if(isset($_GET['tier'])){
			$TierNro = intval($_GET['tier']);
		}
		if(isset($_GET['pla'])){
			$Platform = intval($_GET['pla']);
		}
		if(isset($_GET['size'])){
			$Size = intval($_GET['size']);
		}
		if($TierNro > 0 && $Size > 0 && $Platform > 0){
			displayShowTierConfig($TierNro, $Platform, $Size, 1);
		}
	}
?>