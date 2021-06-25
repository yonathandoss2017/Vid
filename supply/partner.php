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
	
	$ipAddress = $_SERVER['REMOTE_ADDR'];
	$DateTime = date('Y-m-d H:i:s');
	
	//if(in_array($ipAddress, $AutorizedIps)){
		
		$Params = serialize($_POST);
		$Params = mysqli_real_escape_string($db->link, $Params);
		
		if(isset($_POST['name'])){
			$Name = $_POST['name'];
			if(isset($_POST['pmid'])){
				$idAccM = $_POST['pmid'];
				$sql = "SELECT Nick FROM acc_managers WHERE id = '$idAccM' LIMIT 1";
				$PMNick = $db->getOne($sql);
				if(strlen($PMNick) == 2){
					$Name .= '-' . $PMNick;
				}
			}
			
			$Res = newSupplyPartner($Name);
			if($Res == 'unauthorized'){
				logIn('New Partner');
				$Res = newSupplyPartner($Name);
			}
			
			$sql = "INSERT INTO ss_access (Name, Status, IP, Type, Params, DateTime, ErrorCode) VALUES ('$Name', 1, '$ipAddress', 1, '$Params', '$DateTime', '$Res')";
			$db->query($sql);
			
			echo $Res;
		}else{
			$Name = '';
			
			$sql = "INSERT INTO ss_access (Name, Status, IP, Type, Params, DateTime) VALUES ('$Name', 0, '$ipAddress', 1, '$Params', '$DateTime')";
			$db->query($sql);
			
			echo 'no name';
		}
	/*
	}else{
		if($ipAddress == '88.27.142.97'){
			echo rand(1111111,2222222);
		}else{
			echo "unauthorized";
		}
		
	}
	*/