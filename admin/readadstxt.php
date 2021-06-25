<?php
	session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$idtest = 14;
	
	$sql = "SELECT * FROM " . SITES . " WHERE deleted = 0 ORDER BY id DESC";
	$queryS = $db->query($sql);
	if($db->num_rows($queryS) > 0){
		while($Site = $db->fetch_array($queryS)){
			$idUser = $Site['idUser'];
			$idSite = $Site['id'];
			
			$sql = "SELECT deleted FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
			$delUser = $db->getOne($sql);
			
			if($delUser == 0){
				checkAdsTxt($idSite);
				//emailAdsTxt(2, $idSite);
			}
		}
	}
?>