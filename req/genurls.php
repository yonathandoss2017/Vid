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
	

	$handle = fopen("../updates/useragents.csv", "r");
	if ($handle) {
	    while (($line = fgets($handle)) !== false) {
	        $LinesUA = explode(',"',$line);
	    }
	
	    fclose($handle);
	} else {
	    // error opening the file.
	} 
	foreach($LinesUA as $K => $U){
		$LinesUA[$K] = str_replace('"', '', $U);
	}
	//print_r($LinesUA);
	//exit(0);
	
	$Cant = intval($_GET['c']);
	
	$Langs = array('ES', 'EN', 'CL');
	$Countries = array('ES', 'US', 'AR', 'CO', 'PE', 'FR', 'PT');
	
	$devicetypes = array(1,2);
	$publishers = array('64', '125', '322223');
	$pages = array(
		'64'	=> array('8376503'), 
		'125' 	=> array('8376509', '8376508', '8376507'),
		'322223'=> array('7657', '7659')
	);
	$Domains = array('elpais.es', 'lanacion.com.ar', 'rionegro.com.ar', 'lmneuquen.com', 'vidoomy.com');
	$usPrivacys = array("1YN-", "");
	
	$Requests = array();
	
	//echo 'A';
	for($I=0; $I <= $Cant; $I++){
		
		$IP = rand(0,999) . '.' . rand(0,999) . '.' . rand(0,999) . '.' . rand(0,999);
		
		$CA = array_rand($Langs, 1);
		$Lang = $Langs[$CA];
		
		$CA = array_rand($Countries, 1);
		$Country = $Countries[$CA];
		
		$CA = array_rand($publishers, 1);
		$Pub = $publishers[$CA];
		
		$CA = array_rand($pages[$Pub], 1);
		$Page = $pages[$Pub][$CA];
		
		$CA = array_rand($LinesUA, 1);
		$UA = urlencode($LinesUA[$CA]);
		
		$CA = array_rand($devicetypes, 1);
		$Dev = $devicetypes[$CA];
		
		$CA = array_rand($Domains, 1);
		$Domain = $Domains[$CA];
		
		$CA = array_rand($usPrivacys, 1);
		$usP = $usPrivacys[$CA];
		
		
		$Vars = array(
			'id'			=> 'XBJEHGRH' . str_replace(' ', '0', str_pad($I, 10, STR_PAD_LEFT)),
			'ad_type'		=> 0,
			'mimes'			=> 	array("video/mp4"),
			'minduration'	=> 	1,
			'maxduration'	=> 	600,
			'pos'			=>	1,
			'protocols'		=> array(1,2,3,4,5,6,7,8),
			'w'				=> 400,
			'h'				=> 225,
			'skip'			=> 1,
			'ip'			=> $IP,
			'ua'			=> $UA,
			'language'		=> $Lang,
			'devicetype'	=> $Dev,
			'country'		=> $Country,
			'publisher_id'	=> $Pub,
			'site_id'		=> $Site,
			'site_domain'	=> $Domain,
			'site_page' 	=> $Page,
			'coppa' 		=> rand(0,1),
			'gdpr'			=> rand(0,1),
			'us_privacy'	=> $usP
		);
		
		//print_r($Vars);
		//$query = http_build_query($Vars);
		
		//echo 'https://127.0.0.1:8000/?ad_type=0&' . $query . "\n";
		$Requests[] = $Vars;

	}

	//https://127.0.0.1:8000/?ad_type=0&id=testId&mimes[]=video/mp4&minduration=5&maxduration=100&protocols[]=8&protocols[]=6&ip=127.0.0.1&ua=firefox%20browser&country=ES&publisher_id=1234pubId&w=450&h=1200
	header('Content-Type: application/json');
	echo json_encode($Requests, JSON_PRETTY_PRINT);