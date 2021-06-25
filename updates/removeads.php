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
	
	
	$sql = "SELECT * FROM website WHERE publisher_id = 19039";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($Site = $db2->fetch_array($query)){
			
			$idSite = $Site['id'];
			//echo $idSite . "\n";
			
			$sql = "SELECT id FROM ad WHERE website_id = $idSite AND status = 3 LIMIT 1";
			$idAd = $db2->getOne($sql);
			
			$sql = "DELETE FROM ads WHERE id = $idAd AND idSite = $idSite LIMIT 1";
			$db->query($sql);
			echo $sql . "\n";
			
			newGenerateJS($idSite);
			//exit(0);
		}
	}