<?php
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/reports_/adv/config.php');
	require('/var/www/html/login/db.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$h = fopen("test_domains.csv", "r");
	//$C = fgetcsv($h, 1000, ",");
	
	while (($C = fgetcsv($h, 1000, ",")) !== FALSE){
		$Domain = $C[0];
		
		$sql = "SELECT DISTINCT(sites.filename) FROM reports_resume202108 INNER JOIN supplytag ON supplytag.id = reports_resume202108.idTag INNER JOIN sites ON sites.id = supplytag.idSite INNER JOIN reports_domain_names ON reports_domain_names.id = reports_resume202108.Domain WHERE reports_domain_names.Name LIKE '$Domain'";
		$Filename = str_replace( 'http://ads.vidoomy.com', '', str_replace( 'https://ads.vidoomy.com', '', $db->getOne($sql)));
		
		echo '$FN == ' . "'$Filename' || ";
		
	}
	
	