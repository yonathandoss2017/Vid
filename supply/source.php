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
			
			if(isset($_POST['spid'])){
				
				$SPId = intval($_POST['spid']);
				
				if(isset($_POST['env'])){
					
					$Env = intval($_POST['env']);
					
					if($Env == 1 OR $Env == 2){
						
						if(isset($_POST['loop'])){
							$Loop = intval($_POST['loop']);
						}else{
							$Loop = 12;
						}
						
						if($Loop == 0 || $Loop > 12){
							$Loop = 12;
						}
						
						if(isset($_POST['rev'])){
							$Rev = intval($_POST['rev']);
						}else{
							$Rev = 40;
						}
					
						$sql = "INSERT INTO ss_access (Name, SPartner, Status, IP, Type, Params, DateTime) VALUES ('$Name', '$SPId', 1, '$ipAddress', 2, '$Params', '$DateTime')";
						$db->query($sql);
					
						$Res = newSupplySource($Name, $SPId, $Env, $Rev, $Loop);
						if($Res == 'unauthorized'){
							logIn();
							$Res = newSupplySource($Name, $SPId, $Env, $Rev, $Loop);
						}
						echo $Res;
	
					}else{
						
						$sql = "INSERT INTO ss_access (Name, SPartner, Status, IP, Type, Params, DateTime) VALUES ('$Name', '$SPId', 1, '$ipAddress', 2, '$Params','$DateTime')";
						$db->query($sql);
						
						echo 'wrong env';
						
					}
				}else{
					$sql = "INSERT INTO ss_access (Name, SPartner, Status, IP, Type, Params, DateTime) VALUES ('$Name', '$SPId', 2, '$ipAddress', 2, '$Params','$DateTime')";
					$db->query($sql);
					
					echo 'no env';
				}
			}else{
				$sql = "INSERT INTO ss_access (Name, Status, IP, Type, Params, DateTime) VALUES ('$Name', 2, '$ipAddress', 2, '$Params','$DateTime')";
				$db->query($sql);
				
				echo 'no supply partner id';
			}
		}else{
			$Name = '';
			
			$sql = "INSERT INTO ss_access (Name, Status, IP, Type, Params, DateTime) VALUES ('$Name', 2, '$ipAddress', 2, '$Params', '$DateTime')";
			$db->query($sql);
			
			echo 'no name';
		}
	/*
	}else{
		$sql = "INSERT INTO ss_access (Name, Status, IP, Type, Params, DateTime) VALUES ('unauthorized', 10, '$ipAddress', 0, '$Params', '$DateTime')";
		$db->query($sql);
		echo "unauthorized";
	}
	*/