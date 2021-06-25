<?php
	session_start();
	define('CONST',1);
	require('../config.php');
	require('../db.php');
	require('../constantes.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);

	$sql = "SELECT * FROM " . SITES . " ORDER BY id DESC";
	$queryS = $db->query($sql);
	if($db->num_rows($queryS) > 0){
		while($Site = $db->fetch_array($queryS)){
			
			$Url = urlToAdstxt($Site['siteurl']);
			
			$NotFound = false;
			if($AdsText = getAdsTxt($Url)){

			}else{
				if (stripos($Url, 'https') !== false) {
					$Url = str_replace('https', 'http', $Url);
				}else{
					$Url = str_replace('http', 'https', $Url);
				}
				
				if($AdsText = getAdsTxt($Url)){
					
				}else{
					$NotFound = true;
				}
			}
			
			if($NotFound !== true){
				$idUser = $Site['idUser'];
				$idSite = $Site['id'];
				
				//$sql = "SELECT LKQD_id FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
				//$idLkqd = $db->getOne($sql);
				//$idLkqd = 50306;
				
				$Coma = '';
				$N = 0;
				$Mlines = '';
				$Complete = true;
				
				$sql = "SELECT * FROM " . ADSTXT . " ORDER BY id ASC";
				$query = $db->query($sql);
				if($db->num_rows($query) > 0){
					while($Line = $db->fetch_array($query)){
						$N++;
						//$LineTxt = str_replace('{LKQDID}', $idLkqd, $Line['LineTxt']);
						//echo $LineTxt . ' - ';
						
						if($Line['id'] == 1){
							$LineTxt = 'vidoomy.com';
						}else{
							$LineTxt = $Line['LineTxt'];
						}
						
						if (stripos($AdsText, trim($LineTxt)) !== false) {
						    //echo "<span style='color:green;'>True</span><br/>";
						}else{
							//echo "<span style='color:red;'>False</span><br/>";
							$Complete = false;
							$Mlines .= $Coma . $Line['id'];
							$Coma = ',';
						}
					}
				}
				
				if($Complete){
					$sql = "UPDATE " . SITES . " SET adstxt = 0, mlines = '' WHERE id = '$idSite' LIMIT 1";
				}else{
					$sql = "UPDATE " . SITES . " SET adstxt = 1, mlines = '$Mlines' WHERE id = '$idSite' LIMIT 1";
				}
			}else{
				$sql = "UPDATE " . SITES . " SET adstxt = 2, mlines = '' WHERE id = '$idSite' LIMIT 1";
			}
			$db->query($sql);
		}
	}
?>