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
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');
	
	$idSite = 5781;
	$changedWebsiteIds = array();
	$changedAdIds = array();
	
	$sql = "SELECT * FROM ad WHERE dfp = 1 AND status = 1 ORDER BY id DESC LIMIT 5";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($DFPTrue = $db2->fetch_array($query)){
			//echo $DFPTrue['id'] . '-' . $DFPTrue['website_id'];
			if(!in_array($DFPTrue['website_id'], $changedWebsiteIds)){
				$changedWebsiteIds[] = $DFPTrue['website_id'];
			}
			
			if(!in_array($DFPTrue['id'], $changedAdIds)){
				$changedAdIds[] = $DFPTrue['id'];
			}
			
			$sql = "INSERT INTO dfp_ads (idAd, idWebsite) VALUES ('" . $DFPTrue['id'] . "', '" . $DFPTrue['website_id'] . "');";
			//$db->query($sql);
			
			$sql = "UPDATE ads SET DFP = 2 WHERE id = " . $DFPTrue['id'] . " LIMIT 1";
			$db->query($sql);
			
			$sql = "UPDATE ad SET dfp = 2 WHERE id = " . $DFPTrue['id'] . " LIMIT 1";
			$db2->query($sql);
		}
	}
	
	print_r($changedAdIds);
	
	foreach($changedWebsiteIds as $idSite){
		newGenerateJS($idSite);
		echo "GENERATED $idSite \n";
		//exit(0);
	}

	//newGenerateJS($idSite);