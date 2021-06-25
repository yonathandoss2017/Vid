<?php
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	
	echo 8;
	
	require('config.php');
	require('constantes.php');
	require('db.php');
	require('common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);	

	echo '3';
	$file = fopen("worldcitiespop.txt", "r") or exit("Unable to open file!");
	//Output a line of the file until the end is reached
	$n = 0;
	while($Linea = fgets($file)){
		$n++;
		if($n > 1){
			$Line = explode(',',$Linea);
			$CC = strtoupper($Line[0]);
			$Code = str_replace(' ','-',$Line[1]);
			$Name = utf8_encode($Line[2]);
			$sql = "SELECT id FROM countries WHERE country_code = '$CC' LIMIT 1";
			$idCountry = $db->getOne($sql);
			if($idCountry > 0){
				$sql = "INSERT INTO cities (idCountry, Code, Name) VALUES ('$idCountry','$Code','$Name')";
				//$db->query($sql);
			}
			//exit(0);
		}
	}
	fclose($file);




?>