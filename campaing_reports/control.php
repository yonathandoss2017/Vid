<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	require('libs/common.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$mem_var = new Memcached();
	$mem_var->addServer("localhost", 11211);
	
	$idSite = intval($_GET['d']);
	
	if($idSite > 0){
		$sql = "SELECT COUNT(*) FROM interactivecampaings WHERE idSite = '$idSite' AND idCampaing = 1 LIMIT 1";
		if($db->getOne($sql) == 0){ 
			$sql = "INSERT INTO interactivecampaings (idSite, idCampaing) VALUES ('$idSite' , 1)";
			$db->query($sql);
			echo 1;
		}else{
			$sql = "DELETE FROM interactivecampaings WHERE idSite = $idSite AND idCampaing = 1 LIMIT 1";
			$db->query($sql);
			echo 0;
		}
		
		setCacheCampaings();
		//print_r($WhiteList);
		
	}
?>