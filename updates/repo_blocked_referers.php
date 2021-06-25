<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	$ArrayCount = array();
	
	$sql = "SELECT * FROM detdoms_block2 WHERE Event != 1 AND Referer NOT LIKE '%googlesyndication.com%'";
	$query = $db->query($sql);
	while($S = $db->fetch_array($query)){
		$idS = $S['id'];
		$FN = $S['FN'];
		
		$PUrls = parse_url($S['Referer']);
		
		$sql = "SELECT siteurl FROM sites WHERE filename LIKE '%$FN' LIMIT 1";
		$SiteU = $db->getOne($sql);
		
		if(array_key_exists($SiteU, $ArrayCount)){
			if(array_key_exists($PUrls['host'], $ArrayCount[$SiteU])){
				$ArrayCount[$SiteU][$PUrls['host']] = $ArrayCount[$SiteU][$PUrls['host']] + 1;
			}else{
				$ArrayCount[$SiteU][$PUrls['host']] = 1;
			}
		}else{
			$ArrayCount[$SiteU][$PUrls['host']] = 1;
		}
		
	}
	
	$sql = "SELECT * FROM detdoms_block WHERE Event != 1 AND Referer NOT LIKE '%googlesyndication.com%' AND Referer NOT LIKE '%google.com%'";
	$query = $db->query($sql);
	while($S = $db->fetch_array($query)){
		$idS = $S['id'];
		$FN = $S['FN'];
		
		$PUrls = parse_url($S['Referer']);
		
		$sql = "SELECT siteurl FROM sites WHERE filename LIKE '%$FN' LIMIT 1";
		$SiteU = $db->getOne($sql);
		
		if(array_key_exists($SiteU, $ArrayCount)){
			if(array_key_exists($PUrls['host'], $ArrayCount[$SiteU])){
				$ArrayCount[$SiteU][$PUrls['host']] = $ArrayCount[$SiteU][$PUrls['host']] + 1;
			}else{
				$ArrayCount[$SiteU][$PUrls['host']] = 1;
			}
		}else{
			$ArrayCount[$SiteU][$PUrls['host']] = 1;
		}
		
	}
	
	foreach($ArrayCount as $SiteUrl => $BlockedRefs){
		foreach($BlockedRefs as $BlockedRef => $Count){
			echo "$SiteUrl,$BlockedRef,$Count \n";
		}
	}
	//print_r($ArrayCount);