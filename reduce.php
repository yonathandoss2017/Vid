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
	
	$sql = "SELECT * FROM " . STATS . " WHERE idUser = 102 AND Date >= '2018-05-01'";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($St = $db->fetch_array($query)){
			$Impressions = $St['Impressions'];
			$Imr30 = $St['Impressions'] * 30 / 100;
			$Impressions = ceil($Impressions - $Imr30);
			$Revenue = $St['Revenue'];
			$Rev30 = $St['Revenue'] * 30 / 100;
			$Revenue = $Revenue - $Rev30;
			$Revenue = number_format($Revenue, 2, '.', ',');
			$idStat = $St['id'];
			echo $sql = "UPDATE " . STATS . " SET Impressions = '$Impressions', Revenue = '$Revenue' WHERE id = '$idStat'";
			//$db->query($sql);
			echo '<br/>';
		}
	}
?>