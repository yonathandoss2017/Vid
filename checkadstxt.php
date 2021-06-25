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

	
	$Line1 = 'freewheel.tv, 872257, RESELLER';
	$Line2 = 'freewheel.tv, 894193, RESELLER';
	
	//$sql = "SELECT id, siteurl FROM sites ORDER BY id ASC LIMIT 30, 1";
	$sql = "SELECT id, siteurl FROM sites WHERE id = 1515 ORDER BY id ASC";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Site = $db->fetch_array($query)){
			$Url = domainToUrl($Site['siteurl']);
			
			if(substr($Url, -1) != '/'){
				$Url = $Url . '/';
			}
			
			$Url . 'ads.txt';
			
			echo $Url . 'ads.txt' .  " - ";
			

			$Parse = parse_url($Url);
			$Domain = $Parse['host'];
			
			$sql = "SELECT COUNT(*) FROM adstxtericlines WHERE Domain LIKE '$Domain'";
		//	if($db->getOne($sql) == 0){
			if(0 == 0){
			
				$AdsText = getAdsTxt($Url . 'ads.txt');
				
				$Found = 1;
				
				if (stripos(preg_replace('/\s+/', '', $AdsText), preg_replace('/\s+/', '', $Line1)) !== false) {
					echo 'Si ';
				}else{
					echo 'No ';
					$Found = 0;
				}
				if (stripos(preg_replace('/\s+/', '', $AdsText), preg_replace('/\s+/', '', $Line2)) !== false) {
					echo 'Si ';
				}else{
					echo 'No ';
					$Found = 0;
				}
				
				if($Found == 0){
					$Found = 1;
					
					if (stripos($Url, 'https') !== false) {
						$Url = str_replace('https', 'http', $Url);
					}else{
						$Url = str_replace('http', 'https', $Url);
					}
					echo $Url . 'ads.txt';
					echo $AdsText = getAdsTxt($Url . 'ads.txt');	
					
					if (stripos(preg_replace('/\s+/', '', $AdsText), preg_replace('/\s+/', '', $Line1)) !== false) {
						echo 'Si ';
					}else{
						echo 'No ';
						$Found = 0;
					}
					if (stripos(preg_replace('/\s+/', '', $AdsText), preg_replace('/\s+/', '', $Line2)) !== false) {
						echo 'Si ';
					}else{
						echo 'No ';
						$Found = 0;
					}
				}
				
				$Time = time();
				$idSite = $Site['id'];
				$sql = "INSERT INTO adstxtericlines (idSite, Domain, Found, Time) VALUES ('$idSite', '$Domain', '$Found', '$Time')";
				//$db->query($sql);
			}else{
				
				echo "Ignore";
				
			}
			//exit(0);
			echo "\n";
			
		}
	}
	
