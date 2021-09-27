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
	require('/var/www/html/login/admin/libs/display.lib.php');
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	
	/*
	$sql = "SELECT id, showi, ef FROM users ORDER BY id ASC";
	$query = $db->query($sql);
	while($U = $db->fetch_array($query)){
		$idU = $U['id'];
		$NewS = $U['showi'];
		
		$sql = "SELECT id FROM publisher WHERE user_id = '$idU' LIMIT 1";
		$idP = $db2->getOne($sql);
		
		if($idP > 0){
			$sql = "UPDATE publisher SET stats = '$NewS' WHERE id = '$idP' LIMIT 1";
			$db2->query($sql);
			//echo $sql . '<br/>';
			
			if(!is_null($U['ef'])){
				
				if($U['ef'] == 2){
					$FS = 1;
				}else{
					$FS = 2;
				}
				
				$sql = "UPDATE publisher SET fiscal_status = '$FS' WHERE id = '$idP' LIMIT 1";
				$db2->query($sql);
				//echo $sql . '<br/>';
				
			}
		}
		
	}
	*/
	
	$sql = "SELECT id FROM users WHERE AccM = 10 ORDER BY id ASC";
	$query = $db->query($sql);
	while($U = $db->fetch_array($query)){
		$idU = $U['id'];
		
		$sql = "UPDATE publisher SET account_manager_id = 10 WHERE user_id = '$idU' LIMIT 1";	
		$db2->query($sql);
		echo $sql . '<br/>';
			
	}
	
	exit(0);
	
	$sql = "SELECT id, lastlogin FROM users ORDER BY id ASC";
	$query = $db->query($sql);
	while($U = $db->fetch_array($query)){
		$idU = $U['id'];
		$NewS = $U['showi'];
		
		//$sql = "SELECT id FROM publisher WHERE user_id = '$idU' LIMIT 1";
		//$idP = $db2->getOne($sql);
		
		if($U['lastlogin'] > 0){
			
			$LL = date('Y-m-d H:i:s', $U['lastlogin']);
			$sql = "UPDATE user SET last_login = '$LL' WHERE id = '$idU' LIMIT 1";
			$db2->query($sql);
			echo $sql . '<br/>';
			
		}
	}