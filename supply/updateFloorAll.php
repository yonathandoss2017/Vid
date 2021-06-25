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
	
	$NewFloor = 0.2;
	
	$INVALID = 0;
	$VALID = 0;
	
	$sql = "SELECT * FROM supplytag WHERE Old = 0 AND id > 13333 ORDER BY id ASC";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Row = $db->fetch_array($query)){
			$idS = $Row['id'];
			$idTag = intval($Row['idTag']);
	
			$Res = specialUpdateSupplySource($idTag, $NewFloor, true);
			if($Res == 'unauthorized'){
				logIn();
				$Res = specialUpdateSupplySource($idTag, $NewFloor, true);
			}
			if(is_object($Res)){
				if(property_exists($Res, 'errorId')){
					echo $Res->errorId;
					$INVALID++;
				}else{
					$VALID++;
				}
			}else{
				$VALID++;
			}
			
			// invalid-site-ids
			
			echo $idS . ':' . $idTag . "\n";
		}
	}
	
	echo "VALID: $VALID - INVALID: $INVALID \n";