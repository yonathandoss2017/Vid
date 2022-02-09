<?php	
	//exit();
	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 1);
	ini_set('memory_limit', '-1');
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	
	//$sql = "SELECT * FROM sites WHERE deleted = 0 AND id > 18857";
	//$sql = "SELECT * FROM sites WHERE deleted = 0 AND id = 1977";
	$sql = "SELECT DISTINCT(idSite) FROM `supplytag` WHERE `Old` = 1";
	$query2 = $db2->query($sql);
	if($db2->num_rows($query2) > 0){
		while($St = $db2->fetch_array($query2)){
			$idSite = $St['idSite'];
	
	$sql = "SELECT * FROM sites WHERE deleted = 0 AND id = $idSite";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Site = $db->fetch_array($query)){
			$idSite = $Site['id'];
			$idUser = $Site['idUser'];
			
			$FN = $Site['filename'];
			$FN = str_replace('http://ads.vidoomy.com/', '', $FN);
			$FN = str_replace('https://ads.vidoomy.com/', '', $FN);
			
			//if(!file_exists('/var/www/html/ads/newads2/' . $FN)){
				$sql = "SELECT id FROM supplytag WHERE idSite = $idSite AND PlatformType = 1 AND Old != 1 AND TagName NOT LIKE '%intext%' LIMIT 1";
				$ZIDDT = $db->getOne($sql);
				
				$sql = "SELECT id FROM supplytag WHERE idSite = $idSite AND PlatformType = 2 AND Old != 1 AND TagName NOT LIKE '%intext%' LIMIT 1";
				$ZIDMW = $db->getOne($sql);
				
				if($ZIDDT > 0 && $ZIDMW > 0){
					$sql = "SELECT LKQD_id FROM users WHERE id = $idUser LIMIT 1";
					$LKQDid = $db->getOne($sql);
					
					echo "Gen $FN";
					
					$SChain = urlencode("1.0,1!vidoomy.com,$LKQDid,1,");
$NewAd = "var scr = top.document.createElement('script');
scr.src = 'https://vpaid.vidoomy.com/ownplayer-silent/main.js';
scr.onload = function () {
        new top.vidoomy.main.VidoomyPlayer({          
          htmlConfig: {
            type: 'slider',
            width: 640,
            height: 360,
            widthMbl: 400,
            heightMbl: 225
          },
          dataConfig: {
            type: 'round',
            beginCallFiveSecondsToEnd: false,
            schainc: '$SChain',
            schain: '$SChain',
            geolocation: '{{country}}',
            siteId: '$idSite',
            maxServerCalls: 3,
            zoneIdMbl: $ZIDMW,
            zoneId: $ZIDDT,
            logTime: 5000
          },
          player: 'imasdk',
          volume: 0
        }, top);
}
top.document.head.appendChild(scr);";

					
					file_put_contents('/var/www/html/ads/newads2/' . $FN, $NewAd);
					
					echo "1 \n";
					//exit();
				}else{
					echo "NO MW: $ZIDMW DT: $ZIDDT\n";
				}
		}
	}
	
	}
	}