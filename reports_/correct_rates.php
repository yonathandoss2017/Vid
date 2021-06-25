<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
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

function getExchange($Date){
	global $db;
	
	$sql = "SELECT Rate FROM `old_rates` WHERE '$Date' BETWEEN Date1 AND Date2 LIMIT 1";
	return $db->getOne($sql);
}
	
	$DateE = array();

	$sql = "SELECT stats.* FROM stats 
	
	WHERE stats.Date BETWEEN '2020-03-01' AND '2020-03-31'
	 ORDER BY id ASC";// AND users.currency = 1
	 //INNER JOIN users ON users.id = stats.idUser
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Row = $db->fetch_array($query)){
			$idS = $Row['id'];
			
			$RevenueEur = $Row['RevenueEur'];
			$CosteEur = $Row['CosteEur'];
			$Revenue = $Row['Revenue'];
			$Coste = $Row['Coste'];
			
			/*
			if(array_key_exists($Row['Date'], $DateE)){
				$Exchange = $DateE[$Row['Date']];
			}else{
				$Exchange = getExchange($Row['Date']);
				$DateE[$Row['Date']] = $Exchange;
			}
			//echo $Exchange;
			$CosteEur =  $Row['Coste'] / $Exchange;
			$RevenueEur = $Row['Revenue'] / $Exchange;
			*/
			/*
			if($Revenue > 0){
				$RevenueEur = $Revenue - ($Revenue * 20 / 100);
			}else{
				$RevenueEur = 0;
			}
			if($Coste > 0){
				$CosteEur = $Coste - ($Coste * 20 / 100);
			}else{
				$CosteEur = 0;
			}
			*/
			
			
			$sql = "UPDATE stats SET 
				usd_revenue = '$Revenue',
				eur_revenue = '$RevenueEur',
				usd_cost = '$Coste',
				eur_cost = '$CosteEur'				
				WHERE id = $idS LIMIT 1";
			
			//$sql = "UPDATE stats SET RevenueEur = '$RevenueEur', CosteEur = '$CosteEur' WHERE id = $idS LIMIT 1";
			//$db->query($sql);
			$db2->query($sql);
			$db3->query($sql);
			echo $sql . "\n";
			
		}
	}