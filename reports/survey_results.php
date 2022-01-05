<?php	
	@session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('libs/common5.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$Alim = 3;
	
	for($Q = 1; $Q <= 3; $Q++){
		
		echo "\nPregunta $Q \n";
		
		if($Q == 3){
			$Alim = 2;
		}
		
		if($Q == 2){
			$Alim = 5;
		}
		
		if($Q == 1){
			$Alim = 2;
		}
		
		for($A = 1; $A <= $Alim; $A++){
			
			$sql = "SELECT COUNT(*) FROM `surveys` WHERE Question = $Q AND Answer = $A AND Cookie = 1 AND Campaign = 9";
			$Cnt = $db->getOne($sql);
			
			echo "Respuesta $A: $Cnt \n";
				
		}
		
	}