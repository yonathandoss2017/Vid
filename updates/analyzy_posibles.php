<?php
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/reports_/adv/config.php');
	//require('/var/www/html/login/reports_/adv/config_pre.php');
	require('/var/www/html/login/db.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$h = fopen("posibles_quitados.csv", "r");
	//$C = fgetcsv($h, 1000, ",");
	
	while (($C = fgetcsv($h, 1000, ",")) !== FALSE){
		$Domain = $C[0];
		
		$sql = "SELECT SUM(reports_resume202106.formatLoads) AS FL FROM `reports_resume202106` INNER JOIN reports_domain_names ON reports_domain_names.id = reports_resume202106.Domain WHERE reports_resume202106.Date = '2021-06-27' AND reports_domain_names.Name LIKE '$Domain' AND Country NOT IN (3,10,14,15,18,20,28,31,33,38,39,49,55,64,68,70,71,86,87,90,91,100,102,106,110,115,124,142,145,152,190)";
		//exit(0);
		$FLToday = $db->getOne($sql);
		
		$sql = "SELECT SUM(reports_resume202106.formatLoads) AS FL FROM `reports_resume202106` INNER JOIN reports_domain_names ON reports_domain_names.id = reports_resume202106.Domain WHERE reports_resume202106.Date = '2021-06-20' AND reports_domain_names.Name LIKE '$Domain' AND Country NOT IN (3,10,14,15,18,20,28,31,33,38,39,49,55,64,68,70,71,86,87,90,91,100,102,106,110,115,124,142,145,152,190)";
		$FL20 = $db->getOne($sql);
		
		echo '"' . $Domain . '","' . $FLToday . '","' . $FL20 . '"' . "\n";
		
	}
	
	