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
	
	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');
	
	$sql = "SELECT * FROM ads WHERE id > 9115 ORDER BY id ASC";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		
		while($Z = $db->fetch_array($query)){	
			$idAD = $Z['id'];
			$idSite = $Z['idSite'];
			$AdType = $Z['Type'];
			$idLKQD = $Z['idLKQD'];
			$divID = $Z['divID'];
			$width = $Z['Width'];
			$height = $Z['Height'];
			$close = $Z['Close'];
			$dfp = $Z['DFP'];
			$override = $Z['Override'];
			$height_a = $Z['HeightA'];
			$sposition = $Z['SPosition'];
			$ccode = mysqli_real_escape_string($db->link, $Z['CCode']);
			
			if($AdType == 100){
				$AdType = 5;
			}
			
			$created_at = date('Y-m-d H:i:s', $Z['Time']);
			//$sql = "DELETE FROM website_zone WHERE id = '$idZone'";
			//$db2->query($sql);
			
			$sql = "SELECT COUNT(*) FROM ad WHERE id = '$idAD'";
			if($db2->getOne($sql) == 0){
				$sql = "INSERT INTO ad (id, website_id, created_by, ad_type_id, lkqdid, divid, width, height, close, dfp, override, height_a, sposition, ccode, status, created_at)
				VALUES
				($idAD, $idSite, 1, '$AdType', '$idLKQD', '$divID', '$width', '$height', '$close', '$dfp', '$override', '$height_a', '$sposition', '$ccode', 1, '$created_at')";
				$db2->query($sql);
				echo $idAD . ' Agregado <br/>';
			}else{
				$sql = "UPDATE ad SET lkqdid = '$idLKQD', divid = '$divID', width = '$width', height = '$height', close = '$close', dfp = '$dfp', override = '$override', height_a = '$height_a', sposition = '$sposition', ccode = '$ccode' 
				WHERE id = '$idAD' LIMIT 1";
				$db2->query($sql);
				echo $idAD . ' Existe, actualizado <br/>';
			}
			//echo $sql;
			//exit(0);
		}
	}