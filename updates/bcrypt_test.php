<?php	
	//exit();
	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	//require('bcrypt.php');
	
	//echo $hash_for_user = Bcrypt::hash('123123321');
	//echo password_hash('123123123', PASSWORD_BCRYPT);
	
	$sql = "SELECT password FROM users WHERE id = '26068'";
	$CPass = $db->getOne($sql);
	
	if(password_verify('12312312', $CPass)){
		echo 'Si';
	}else{
		echo 'No';
	}