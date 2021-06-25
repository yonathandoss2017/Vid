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
	require('/var/www/html/login/supply/authorized.php');
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie4.txt';
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
		
	
	
	//print_r($Res);
	
	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	logIn();
	
	$sql = "SELECT * FROM ad WHERE ad_type_id != 5 AND lkqdid != '' AND status = 1 AND id < 3649 ORDER BY id DESC";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($Ad = $db2->fetch_array($query)){
			$idAd = $Ad['id'];
			
			$Res = getSupplySourceNameLoopRev($Ad['lkqdid']);
			
			if(is_array($Res)){
				$Name = $Res['Name'];
				$Loop = $Res['Loop'];
				$Rev = $Res['Rev'];
				
				$sql = "UPDATE ad SET name = '$Name', ad_loop = '$Loop', revenue = '$Rev' WHERE id = '$idAd' LIMIT 1";
				$db2->query($sql);
				echo $sql . "\n";
			}else{
				echo "No Encontrado\n";
			}
			//exit(0);
		}	
	}