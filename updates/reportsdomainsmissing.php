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
	
	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$dbuser3 = "root";
	$dbpass3 = "vidooDev-Pass_2020";
	$dbhost3 = "publisher-panel-for-dev.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname3 = "vidoomy";
	$db3 = new SQL($dbhost3, $dbname3, $dbuser3, $dbpass3);
	
	
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