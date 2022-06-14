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
	
	$sql = "SELECT * FROM `reports_country_names` WHERE Code IN('DE', 'AT', 'BE', 'BG', 'CZ', 'CY', 'HR', 'DK', 'EE', 'FI', 'FR', 'GR', 'HU', 'IS', 'IE', 'IT', 'LI', 'LV', 'LT', 'LU', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB')";
	$Coma = '';
	$idCountryList = '';
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Country = $db->fetch_array($query)){
			
			$idCountryList .= $Coma . $Country['id'];
			$Coma = ',';
			
		}
	}
	
	$Query = "SELECT Domain, SUM(sum_FormatLoads) AS FormatLoads FROM production_enriched_event_supply WHERE __time >= '2021-06-27 00:00:00' AND  __time <= '2021-06-27 23:00:00' GROUP BY Domain ORDER BY 2 DESC";
	
	$ch = curl_init( $druidUrl );
	
	$context = new \stdClass();
	$context->sqlOuterLimit = 30000;//;
	
	$payload = new \stdClass();
	$payload->query = $Query;
	$payload->resultFormat = 'array';
	$payload->header = true;
	$payload->context = $context;
	
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($payload) );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec($ch);
	curl_close($ch);
	$result = json_decode($result) ;
	
	$Compare = array();
	
	foreach($result as $kres => $res){
		if($kres >= 1){
	
			$Domain = $res[0];
			$FormatLoads = $res[1];
	
			$Compare[$Domain] = $FormatLoads;
		}
	}
	
	
	
	$sql = "SELECT reports_domain_names.Name AS Domain, SUM(reports_resume202106.formatLoads) AS FL FROM `reports_resume202106` INNER JOIN reports_domain_names ON reports_domain_names.id = reports_resume202106.Domain WHERE Date = '2021-06-20' AND Country IN (3,10,14,15,18,20,28,31,33,38,39,49,55,64,68,70,71,86,87,90,91,100,102,106,110,115,124,142,145,152,190) GROUP BY Domain ORDER BY FL DESC";
	
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		echo '"Domain","FormatLoads LKQD","FormatLoads Vidoomy"';
		
		while($Data = $db->fetch_array($query)){
			
			if(array_key_exists($Data['Domain'], $Compare)){
				$VDMFL = $Compare[$Data['Domain']];
			}else{
				$VDMFL = 0;
			}
			
			echo '"' . $Data['Domain'] . '","' . $Data['FL'] . '","' . $VDMFL . '"' . "\n";
			
		}
	}
	
	
	