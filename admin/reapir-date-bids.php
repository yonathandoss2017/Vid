<?php
	session_start();
	define('CONST',1);
	if($_SESSION['Type'] == 1 || $_SESSION['Type'] == 3){
		
	}else{
		header('Location: login.php');
		exit(0);
	}
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);

	$sql = "SELECT id FROM " . PREBID_IMPRESION . " WHERE Date = '2019-02-27' AND Mobile = 1";
	$query = $db->query($sql);
	$N = 0;
	$NW = 0;
	if($db->num_rows($query) > 0){
		while($Imp = $db->fetch_array($query)){
			$N++;
			$sql = "SELECT Winner FROM " . PREBID_BIDS . " WHERE idImpesion = '" . $Imp['id'] . "'";
			$NW += intval($db->getOne($sql));
		}
	}
	
												
	echo $N . '/' . $NW;
?>