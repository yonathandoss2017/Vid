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
		
	$dbuser2 = "root";
	$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$NoRev = 0;
	$NoRev2019 = 0;
	$SiRev = 0;

	$sql = "SELECT * FROM user WHERE allow_publisher_payments = 0 AND status = 1";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($U = $db2->fetch_array($query)){
			$idUser = $U['id'];
			$User = $U['username'];
			
			$sql = "SELECT SUM(Revenue) FROM stats WHERE idUser = $idUser";
			$Rev = $db->getOne($sql);

			if($Rev > 0){

				$sql = "SELECT SUM(Revenue) FROM stats WHERE idUser = $idUser AND Date >= '2019-01-01'";
				$Rev2019 = $db->getOne($sql);
				
				if($Rev2019 > 0){
					
					$SiRev++;
					
				}else{
					
					$NoRev2019++;
					//echo $User . "\n";
					$sql = "UPDATE user SET allow_publisher_payments = 1 WHERE id = $idUser";
					$db2->query($sql);
					
				}
			}else{
				/*
				$sql = "UPDATE user SET allow_publisher_payments = 1 WHERE id = $idUser";
				$db2->query($sql);
				*/
			}
		}
	}
	
	echo "NoRev $NoRev \n";
	echo "NoRev2019 $NoRev2019 \n";
	echo "SiRev $SiRev";