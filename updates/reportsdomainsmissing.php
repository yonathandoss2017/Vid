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
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	$db3 = new SQL($pubDev01['host'], $pubDev01['db'], $pubDev01['user'], $pubDev01['pass']);
	
	
	$sql = "SELECT * FROM reports_domain_names WHERE id >= 108249";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Sta = $db->fetch_array($query)){
			$idDomain = $Sta['id'];
			$DomainS = $Sta['Name'];
				
			$sql = "INSERT INTO report_domain (id, name) VALUES ($idDomain, '$DomainS')";
			//$db2->query($sql);
			$db3->query($sql);
			
		}
	}