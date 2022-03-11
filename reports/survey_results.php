<?php	
	@session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('libs/common5.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$Alim = 2;
	
	for($Q = 1; $Q <= 4; $Q++){
		
		echo "\nPregunta $Q \n";
		
		if($Q == 4){
			$Alim = 4;
		}
		
		if($Q == 3){
			$Alim = 4;
		}
		
		if($Q == 2){
			$Alim = 4;
		}
		
		if($Q == 1){
			$Alim = 4;
		}
		
		for($A = 1; $A <= $Alim; $A++){
			
			$sql = "SELECT COUNT(*) FROM `surveys` WHERE Question = $Q AND Answer = $A AND Cookie = 0 AND Campaign = 13";
			$Cnt = $db->getOne($sql);
			
			echo "Respuesta $A: $Cnt \n";
				
		}
		
	}