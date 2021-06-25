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
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	require('/var/www/html/login/admin/lkqdimport/common.php');
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$DateSRate = array();
	$DateP = '';
	$Table = 'reports202103';
	
	$sql = "SELECT * FROM $Table WHERE Revenue > 0 ORDER BY id DESC";
	
	$query = $db->query($sql);
	while($Da = $db->fetch_array($query)){
		
		$idRow = $Da['id'];
		$Date = $Da['Date'];
		$Revenue = $Da['Revenue'];
		$Coste = $Da['Coste'];
		
		if($DateP != $Date){
			$DateP = $Date;
			echo $Date . "\n";
		}
		
		//CONVERSION A EUROS
		if(array_key_exists($Date, $DateSRate)){
			$Rate = $DateSRate[$Date];
			//echo "Array get \n";
	    }else{
			$sql = "SELECT Rate FROM rates WHERE Date = '$Date'";
			$Rate = $db->getOne($sql);
			
			
			//echo "SQL get \n";
			if($Rate <= 0){
				$DateForRate = new DateTime($Date);
				$DateForRate->modify('-1 day');
				
			    $JsonRates = file_get_contents('https://api.exchangeratesapi.io/' . $DateForRate->format('Y-m-d') . '?access_key=7df9c3b2eb8318c2294112c50f5209c8');
			    $Rates = json_decode($JsonRates);
			    $Rate = $Rates->rates->USD;
			    
			    $sql = "INSERT INTO rates (Date, Rate) VALUES ('$Date', '$Rate')";
			    $db->query($sql);
			    
			    //echo "API get \n";
			    $DateSRate[$Date] = $Rate;
			}else{
				$DateSRate[$Date] = $Rate;
			}
		}
		
		if($Rate == 0){
			echo "RATE 0";
			exit();
		}
		
		if($Coste > 0){
			$CosteEur = $Coste / $Rate;
		}else{
			$CosteEur = 0;
		}
		if($Revenue > 0){
			$RevenueEur = $Revenue / $Rate;
		}else{
			$RevenueEur = 0;
		}
		
		//echo "Rate: $Rate Coste: $Coste CosteEur: $CosteEur Revenue: $Revenue RevenueEur: $RevenueEur \n";
		
		$sql = "UPDATE $Table SET CosteEur = '$CosteEur', RevenueEur = '$RevenueEur' WHERE id = $idRow LIMIT 1";
		$db->query($sql);
	}