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
	//sleep(10);
	//exit();
	//print_r($_POST);
	
	//if(in_array($ipAddress, $AutorizedIps)){
	
		if(isset($_POST['sid'])){
			
			$sID = intval($_POST['sid']);
			if($sID > 0){
				
				if(isset($_POST['loop'])){
					$Loop = intval($_POST['loop']);
				}else{
					$Loop = 12;
				}
				
				if($Loop > 12){
					$Loop = 12;
				}
				
				if(isset($_POST['rev'])){
					$Rev = intval($_POST['rev']);
				}else{
					$Rev = 40;
				}
				
				if(isset($_POST['name'])){
					$Name = $_POST['name'];
				}else{
					$Name = '';
				}
			
				$sql = "INSERT INTO ss_access (Name, SPartner, Status, IP, Type, DateTime) VALUES ('', '$sID', 1, '$ipAddress', 3, '$DateTime')";
				$db->query($sql);
				
				//$Res = updateSupplySource($sID, $Name, $Rev, $Loop);
				$Res = 'unauthorized';
				if($Res == 'unauthorized'){
					logIn('Source update');
					$Res = updateSupplySource($sID, $Name, $Rev, $Loop);
				}
				echo $Res;
					
			}else{
				$sql = "INSERT INTO ss_access (Name, Status, IP, Type, DateTime) VALUES ('$Name', 2, '$ipAddress', 3, '$DateTime')";
				$db->query($sql);
				
				echo 'no supply source id';
			}
		}else{
			$Name = '';
			
			$sql = "INSERT INTO ss_access (Name, Status, IP, Type, DateTime) VALUES ('$Name', 2, '$ipAddress', 3, '$DateTime')";
			$db->query($sql);
			
			echo 'no supply source id';
		}
	/*
	}else{
		echo "unauthorized";
	}
	*/