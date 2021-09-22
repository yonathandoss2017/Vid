<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
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
	

	$DateSRate = array();
	$Currencies = array();
	$CosteEur = 0;
	$RevenueEur = 0;

	$sql = "SELECT * FROM stats WHERE Date BETWEEN '2020-04-01' AND '2020-04-11' AND Coste > 0 ORDER BY id ASC";
	$query = $db->query($sql);
	while($S = $db->fetch_array($query)){
		$idS = $S['id'];
		$Revenue = $S['Revenue'];
		$Coste = $S['Coste'];
		$idU = $S['idUser'];
		$Date = $S['Date'];
		
		if(array_key_exists($idU, $Currencies)){
			$Currency = $Currencies[$idU];
		}else{
			$sql = "SELECT currency FROM users WHERE id = '$idU' LIMIT 1";
			$Currency = $db->getOne($sql);
			
			$Currencies[$idU] = $Currency;
		}
		
		if($Currency == 1){
			if(array_key_exists($Date, $DateSRate)){
				$Rate = $DateSRate[$Date];
		    }else{
			    $DateForRate = new DateTime($Date);
			    $DateForRate->modify('-1 day');
			    
			    $JsonRates = file_get_contents('https://api.exchangeratesapi.io/' . $DateForRate->format('Y-m-d'));
			    $Rates = json_decode($JsonRates);
			    $Rate = $Rates->rates->USD;
			    
			    $DateSRate[$Date] = $Rate;
		    }

			if($Rate > 0){
				if($Revenue > 0){
					$RevenueEur = $Revenue / $Rate;
				}else{
					$RevenueEur = 0;
				}
				if($Revenue > 0){
					$CosteEur = $Coste / $Rate;
				}else{
					$CosteEur = 0;
				}
			
			}else{
				die('Error' . $idS);
			}
		}else{
			$RevenueEur = $Revenue - ($Revenue * 20 / 100);
			$CosteEur = $Coste - ($Coste * 20 / 100);
		}
		
		echo $sql = "UPDATE stats SET RevenueEur = '$RevenueEur', CosteEur = '$CosteEur' WHERE id = '$idS' LIMIT 1";
		echo "\n";
		$db->query($sql);
		
		$sql = "UPDATE stats SET eur_revenue = '$RevenueEur', eur_cost = '$CosteEur' WHERE id = '$idS' LIMIT 1";
		$db2->query($sql);
		
		$sql = "UPDATE stats SET eur_revenue = '$RevenueEur', eur_cost = '$CosteEur' WHERE id = '$idS' LIMIT 1";
		$db3->query($sql);
	}