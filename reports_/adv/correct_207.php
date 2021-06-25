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
	
	$db = new SQL($dbhost, 'vidoomy_adv', $dbuser, $dbpass);
	
	$sql = "SELECT * FROM reports WHERE idCampaing = 207";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Row = $db->fetch_array($query)){
			$idRow = $Row['id'];
			
			$Impressions = $Row['Impressions'];
			
			$Rev = $Impressions * 7 / 1000;
			
			$sql = "UPDATE reports SET Revenue = '$Rev' WHERE id = $idRow LIMIT 1";
			//echo $sql;
			$db->query($sql);
		}
	}