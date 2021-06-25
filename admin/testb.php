<?php 
	@session_start();
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php'); 
	require('../db.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$Sum = 0;
	$Array = array();
	$sql = "SELECT * FROM " . ADS . " WHERE idLKQD != '' ";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Ad = $db->fetch_array($query)){
			$N++;
			
			//echo $Ad['idLKQD'];
			//echo '<br/>';
			$sql = "SELECT id FROM supplytag WHERE idTag = '" . $Ad['idLKQD'] . "' ";
			$idTag = $db->getOne($sql);
			
			if(!in_array($idTag, $Array)){
				$Array[] = $idTag;
				
				$sql = "SELECT SUM(Revenue) FROM stats WHERE idTag = '" . $idTag . "' AND stats.Date = '2018-11-26' ";
				$Sum += $db->getOne($sql);
			}
			/*
			if($N >= 20){
				break;
			}
			*/
		}
	}
	echo $Sum;