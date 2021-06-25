<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('config.php');
	require('constantes.php');
	require('db.php');
	require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	
	$sql = "SELECT * FROM " . TAGS . " WHERE idSite > 0";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($dataTag = $db->fetch_array($query)){
			$TagID = $dataTag['id'];
			$idSite = $dataTag['idSite'];
			$sql = "UPDATE " . STATS . " SET idSite = '$idSite' WHERE idTag = '$TagID' ";
			$db->query($sql);
		}
	}else{
		echo 'Sin registros';
	}