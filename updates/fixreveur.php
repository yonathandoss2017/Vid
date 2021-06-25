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
	//require('countries.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	require('/var/www/html/login/admin/libs/display.lib.php');
	
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

	
	$N = 0;
	
	$sql = "SELECT id FROM users WHERE currency = 2";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($U = $db->fetch_array($query)){
			$idUser = $U['id'];
			
			/*
			$sql = "SELECT * FROM stats WHERE idUser = $idUser AND Date >= '2020-04-01'";
			$query2 = $db->query($sql);
			if($db->num_rows($query2) > 0){
				while($Stat = $db->fetch_array($query2)){
					$N++;
					echo $idStat = $Stat['id'];
					
					$EurCost = correctCurrency($Stat['Coste'], 2);
					$EurRevenue = correctCurrency($Stat['Revenue'], 2);
					
					$sql = "UPDATE stats SET CosteEur = '$EurCost', RevenueEur = '$EurRevenue' WHERE id = $idStat LIMIT 1";
					$db->query($sql);
					if($N >= 1000){
						$N = 0;
						echo "1000 more \n";
					}
				}
			}
			*/
			
			$sql = "SELECT * FROM stats WHERE iduser = $idUser AND date >= '2020-04-01'";
			$query2 = $db3->query($sql);
			if($db3->num_rows($query2) > 0){
				while($Stat = $db3->fetch_array($query2)){
					$N++;
					echo $idStat = $Stat['id'];
					
					$EurCost = correctCurrency($Stat['usd_cost'], 2);
					$EurRevenue = correctCurrency($Stat['usd_revenue'], 2);
					
					$sql = "UPDATE stats SET eur_cost = '$EurCost', eur_revenue = '$EurRevenue' WHERE id = $idStat LIMIT 1";
					$db3->query($sql);
					if($N >= 1000){
						$N = 0;
						echo "1000 more \n";
					}
				}
			}
			
			
			
			
		}		
	}