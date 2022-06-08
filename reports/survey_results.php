<?php	
	@session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('libs/common5.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$Campaign = 26;
	$Questions = 2;
	$Cookie = 1;
	
	$Question[1] = 2;
	$Question[2] = 2;
	//$Question[3] = 2;
	//$Question[4] = 2;
	//$Question[5] = 2;
	
	
	
	for($Q = 1; $Q <= $Questions; $Q++){
		
		echo "\nPregunta $Q \n";
				
		for($A = 1; $A <= $Question[$Q]; $A++){
			
			$sql = "SELECT COUNT(*) FROM `surveys` WHERE Question = $Q AND Answer = $A AND Cookie = $Cookie AND Campaign = $Campaign";
			$Cnt = $db->getOne($sql);
			
			echo "Respuesta $A: $Cnt \n";
				
		}
		
	}