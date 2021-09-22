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
	$db3 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	$sql = "SELECT * FROM finance_account WHERE currency_id = 1";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($Acc = $db2->fetch_array($query)){
			$idAcc = $Acc['id'];
			//echo "AID: $idAcc";
			$sql = "SELECT COUNT(*) FROM publisher WHERE finance_account_id = $idAcc";
			$CNT = intval($db2->getOne($sql));
			
			//echo " ($CNT)";
			
			$sql = "SELECT * FROM publisher WHERE finance_account_id = $idAcc";
			$query2 = $db3->query($sql);
			if($db3->num_rows($query2) > 0){
				while($Pub = $db3->fetch_array($query2)){
					$UserId = $Pub['user_id'];
					
					$sql = "SELECT username FROM user WHERE id = $UserId LIMIT 1";
					$User = $db2->getOne($sql);
					
					$sql = "SELECT enable_new FROM users WHERE id = $UserId LIMIT 1";
					if($db->getOne($sql) == 1){
						$Mig = 'MIGRATED ';
					}else{
						$Mig = '';
						
						$PubsByAcc[$Pub['account_manager_id']][] = $User;
					}
					
					//echo " Pub: $User $Mig";
				}
			}
			//echo "\n";
		}
	}
	
	
	foreach($PubsByAcc as $Acc => $Pubs){
		
		$sql = "SELECT CONCAT(name,' ', last_name) FROM user WHERE id = $Acc";
		echo "\n\n" . $db2->getOne($sql) . "\n";
		
		foreach($Pubs as $Pub){
			echo $Pub . "\n";
		}
		
	}