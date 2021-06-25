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


	$CheckDoms = array();
	
	$sql = "SELECT * FROM `adstxt_triplelift`
		WHERE Found = 1 AND Has = 0";
		
	$query = $db->query($sql);
	while($S = $db->fetch_array($query)){
		$idSite = $S['idSite'];
		//echo $S['Domain'] . ": ";
		
		if(!in_array($S['Domain'], $CheckDoms)){
			$CheckDoms[] = $S['Domain'];
			
			$sql = "SELECT SUM(FormatLoads) FROM stats WHERE idSite = $idSite AND Date >= '2020-04-01'";
			//echo $sql . "\n";
			$Cnt = $db->getOne($sql);
			
			if($Cnt >= 50000){
				//echo $sql;
				if($S['Domain'] == 'youtube.com' || $S['Domain'] == 'facebook.com'){
					echo "AA" . $S['Domain'] . "\n";
					
					exit(0);
				}
				echo $S['Domain'] . "\n";
				
			}
		}
	}