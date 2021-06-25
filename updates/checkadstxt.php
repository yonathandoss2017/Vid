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
	/*
	
	$sql = "SELECT Domain FROM adstxtericlines WHERE Found = 0 ORDER BY id ASC";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Site = $db->fetch_array($query)){
			if($Site['Domain'] != 'htpp'){
				echo '<a href="http://' . strtolower($Site['Domain']) . '/ads.txt" targer="_blank">' . str_replace('www.', '', strtolower($Site['Domain'])) . '' . '</a><br/>';
			}
		}
	}
	
	*/
	
	$Line1 = 'triplelift.com';
	//$Line1 = 'adtech.com';
	
	//$sql = "SELECT id, siteurl FROM sites ORDER BY id ASC LIMIT 30, 1";
	$sql = "SELECT id, siteurl FROM sites WHERE deleted = 0 AND id < 5365 ORDER BY id DESC";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Site = $db->fetch_array($query)){
			$idSite = $Site['id'];
			
			$Url = domainToUrl($Site['siteurl']);
			
			if(substr($Url, -1) != '/'){
				$Url = $Url . '/';
			}
			
			$Url . 'ads.txt';
			
			echo $Url . 'ads.txt: ';
			
			$Parse = parse_url($Url);
			$Domain = $Parse['host'];
			
			$AdsText = getAdsTxt($Url . 'ads.txt');
			
			$Found = 0;
			$Has = 0;
			
			if($AdsText !== false){
				$Found = 1;
				if (stripos(preg_replace('/\s+/', '', $AdsText), preg_replace('/\s+/', '', $Line1)) !== false) {
					echo 'Si ';
					$Has = 1;			
				}else{
					echo 'No ';
					$Has = 0;
				}
			}else{
				if (stripos($Url, 'https') !== false) {
					$Url = str_replace('https', 'http', $Url);
				}else{
					$Url = str_replace('http', 'https', $Url);
				}
				
				//echo $Url . 'ads.txt';
				$AdsText = getAdsTxt($Url . 'ads.txt');	
				
				if($AdsText !== false){
					$Found = 1;
					if (stripos(preg_replace('/\s+/', '', $AdsText), preg_replace('/\s+/', '', $Line1)) !== false) {
						echo 'Si ';
						$Has = 1;
					}else{
						echo 'No ';
						$Has = 0;
					}
				}else{
					echo "Not Found";
				}
			}
			echo "\n";
			$sql = "INSERT INTO adstxt_triplelift (idSite, Found, Has, Domain) VALUES ($idSite, $Found, $Has, '$Domain')";
			$db->query($sql);
		}
	}
	
	