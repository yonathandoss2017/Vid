<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	
	$N = 0;
	
	$CSVT = file_get_contents('import2.csv');
	
	$CSVT = nl2br($CSVT);
	
	$ArCSV = explode('<br />', $CSVT);
	
	
	foreach($ArCSV as $CsvLine){
		$N++;
		
		echo $CsvLine;
		
		if($N >= 10){
			exit(0);
		}
	}