<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$sql = "SELECT * FROM " . TAGS . " WHERE idUser = 56";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Tag = $db->fetch_array($query)){
			$idSite = $Tag['idSite'];
			$idTag = $Tag['id'];
			$sql = "UPDATE " . STATS . " SET idSite = '$idSite' WHERE idTag = '$idTag'";
			$db->query($sql);
		}
	}
?>