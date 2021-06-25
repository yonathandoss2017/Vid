<?php	
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


	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	$sql = "SELECT * FROM supplytag WHERE idPlatform = 1 AND Old != 1 AND id > 7000 ORDER BY id ASC";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		
		while($Z = $db->fetch_array($query)){	
			echo $idZone = $Z['id'];
			$idSite = $Z['idSite'];
			$Name = $Z['TagName'];
			$ZoneId = $Z['idTag'];
			$PlatformType = $Z['PlatformType'];
			
			//$sql = "DELETE FROM website_zone WHERE id = '$idZone'";
			//$db2->query($sql);
			
			$sql = "SELECT COUNT(*) FROM website_zone WHERE id = '$idZone'";
			if($db2->getOne($sql) == 0){
			
				echo $sql = "INSERT INTO website_zone (id, website_id, name, zone_id, platform, created_by, tracing_status)
				VALUES
				($idZone, $idSite, '$Name', '$ZoneId', $PlatformType, 1, 0)";
				$db2->query($sql);
				
				echo '<br/>';
			}else{
				echo 'Existe <br/>';
			}
			//echo $sql;
			//exit(0);
		}
	}