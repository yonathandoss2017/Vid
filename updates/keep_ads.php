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
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');
	
	
	$sql = "SELECT * FROM ad WHERE id >= 4368 AND id <= 4369";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($Ad = $db2->fetch_array($query)){
			
			$idAd = $Ad['id'];
			
			$sql = "SELECT COUNT(*) FROM ads WHERE id = '$idAd' LIMIT 1";		
			if($db->getOne($sql) == 0){
				
				$Date = date('Y-m-d');
				$Time = time();
				
				$idAd = $Ad['id'];
				$idSite = $Ad['website_id'];
				$idLkqd = $Ad['lkqdid'];
				$divID = $Ad['divid'];
				$Type = $Ad['ad_type_id'];
				$Width = $Ad['width'];
				$Height = $Ad['height'];
				$Close = $Ad['close'];
				$DFP = $Ad['dfp'];
				$Override = $Ad['override'];
				$AA = $Ad['height_a'];
				$Spos = $Ad['sposition'];
				$CCode = $Ad['ccode'];
				
				$sql = "INSERT INTO ads 
				(id, idSite, idSCode, idLKQD, divID, Type, Width, Height, Close, DFP, Override, HeightA, SPosition, CCode, Time, Date) 
				VALUES 
				('$idAd', '$idSite','0','$idLkqd','$divID','$Type','$Width','$Height','$Close','$DFP','$Override','$AA','$Spos',\"$CCode\",'$Time','$Date')";
				$db->query($sql);
				
				newGenerateJS($idSite);
			}
		}
	}