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
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	$sql = "SELECT * FROM supplytag WHERE currency = 0 ORDER BY id DESC";
	$query = $db->query($sql);
	while($Da = $db->fetch_array($query)){
		
		$idRow = $Da['id'];
		$idUser = $Da['idUser'];
		
		$sql = "SELECT finance_account_id FROM publisher WHERE user_id = $idUser";
		$FinanceID = $db2->getOne($sql);
		
		if($FinanceID > 0){
		
			$sql = "SELECT currency_id FROM finance_account WHERE id = $FinanceID LIMIT 1";
			$Currency = $db2->getOne($sql);
			
			if($Currency > 0){
			
				$sql = "UPDATE supplytag SET currency = $Currency WHERE id = $idRow LIMIT 1";
				$db->query($sql);
				
				echo $idRow . ' - ' . $Currency . "\n";
			
			}else{
				
				echo $idRow . ' No CURRENCY Found ' . "\n";
				
			}
		}else{
			echo $idRow . ' No Finance ID Found ' . "\n";
		}
	}
		