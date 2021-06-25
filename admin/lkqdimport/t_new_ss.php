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
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	require('/var/www/html/login/admin/lkqdimport/common.php');
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie4.txt';
	
	/*
	echo $idLKQD = newSupplyPartner('test_auto_5');
	
	exit(0);
	if($idLKQD == 'unauthorized'){
		echo 'Loggin in...';
		logIn();
		$idLKQD = newSupplyPartner('test_auto_5');
		
		if(intval($idLKQD))
	}
	

	echo $idLKQD;
	exit();
	*/
	
function newSupplySource($SName, $SPId, $Env = 1, $Loop = 12, $debug = false){
	global $cookie_file;
	
	if($Env == 1){
		$environmentId = 3;
		$URL = "https://api.lkqd.com/supply-tags/find-by-id?siteId=909242";
	}else{
		$environmentId = 1;
		$URL = "https://api.lkqd.com/supply-tags/find-by-id?siteId=909244";
	}
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
		
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
	$result = curl_exec($ch);
	curl_close($ch); 
	
	$Data = json_decode($result);
	//print_r($Data);
	
	$tagSiteAssociations = array();
	
	foreach($Data->associations as $D){
		if(property_exists($D, 'priority')){
			$tagSiteAssociations[] = array(
				'tagId' => $D->tagId,
				'priority' => $D->priority
			);
		}else{
			$tagSiteAssociations[] = array(
				'tagId' => $D->tagId,
				'priority' => null
			);
		}
	}
	
	$URL = 'https://api.lkqd.com/supply/sources';
	
	$LastU = date('Y-m-d\TH:i:s.') . rand(100,999) . 'Z';

	$RequestPayload = array(
		"domain" => null,
		"adType" => "video",
		"siteType" => "normal",
		"status" => "active",
		"relationship" => "owned",
		"cpmFloor" => 2,
		"siteCost" => 40,
		"siteCostType" => "rev",
		"maxVideoAdDurationSec" => null,
		"sessionMaxOpportunities" => null,
		"sessionMaxImpressions" => $Loop,
		"sessionMinIntervalMs" => null,
		"minVpsr" => null,
		"strictMode" => false,
		"enforcePlatformConnections" => false,
		"allowFloorsPassedIn" => false,
		"serverSideVpaidAllowMediafiles" => "agency",
		"passbackTagType" => null,
		"demandTargetingType" => "open",
		"prebidFilters" => array(
			"whiteOps" => array("enabled" => false),
			"pixalate" => array("enabled" => false),
			"brightcom" => array("enabled" => false),
			"doubleVerify" => array("enabled" => false)
		),
		"lastUpdatedAt" => "$LastU",
		"tagSiteAssociations" => $tagSiteAssociations,
		"vastEnabledDesktop" => null,
		"vastEnabledMobileWeb" => null,
		"vastEnabledMobileApp" => null,
		"vastEnabledCtv" => null,
		"vastVpaidEnabledDesktop" => null,
		"vastVpaidEnabledMobileWeb" => null,
		"vastVpaidEnabledMobileApp" => null,
		"execution" => array(),
		"audienceCharacteristics" => array(),
		"serverSideAdInsertion" => false,
		"serverSideAdInsertionVendor" => null,
		"precache" => false,
		"precacheSec" => null,
		"precachingMaxAgeSec" => null,
		"verifiedDate" => null,
		"declinedDate" => null,
		"declineComment" => null,
		"submittedForVerificationDate" => null,
		"verificationState" => "unverified",
		"_verificationState" => "unverified",
		"_execution" => array(),
		"_audienceCharacteristics" => array(),
		"partnerId" => $SPId,
		"environmentId" => $environmentId,
		"siteName" => "$SName",
		"cpmFloorDemand" => 1.5,
		"maxDesktopAsyncSlots" => null
	);
	
	//print_r($RequestPayload);
	
	$RequestPayloadJson = json_encode($RequestPayload);
	
	$Headers = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $RequestPayloadJson);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);

	$result = curl_exec($ch);
	curl_close($ch); 

	$Data = json_decode($result);
	print_r($Data);
	
	if(array_key_exists('siteId', $Data)){
		return $Data->siteId;
	}else{
		if($debug){
			return $result;
		}else{
			return false;
		}
	}
}

	echo newSupplySource('test_a_1_mw', 56998, 2, 13, true);