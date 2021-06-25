<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	require('../admin/libs/display.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);


	$Archivo = file_get_contents('dom.csv');
	$Archivo = nl2br($Archivo);
	$ArLines = explode('<br />',$Archivo);
	
	$Array = array();
	$N = 0;
	foreach ($ArLines as $linea) {
		$linea = trim($linea);
		
		$sql = "SELECT filename FROM sites WHERE siteurl LIKE '%$linea%' AND deleted = 0 LIMIT 1";
		$Filename = $db->getOne($sql);
		
		$sql = "SELECT id FROM sites WHERE siteurl LIKE '%$linea%' AND deleted = 0 LIMIT 1";
		$idSite = $db->getOne($sql);
		
		if($Filename == ''){
			//echo $linea . " No encontrado<br/>";
		}
		
		$arF = explode('/', $Filename);
		$FN = $arF[3];
		if(!in_array($FN, $Array)){
			echo "$idSite => '/" . $FN . "', \n";
		}
	}