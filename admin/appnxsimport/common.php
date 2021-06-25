<?php
	
function logIn(){
	global $sessionId, $cookie_file;
	
	$headers1 = array(
		"sec-fetch-dest" => "document",
		"sec-fetch-mode" => "navigate",
		"sec-fetch-site" => "none",
		"sec-fetch-user" => "?1",
		"upgrade-insecure-requests" => "1",
		"user-agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36"
	);
	
	$url = 'https://console.appnexus.com/login';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers1);
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
	$result = curl_exec($ch);
	curl_close($ch); 
	
	$post = array(
		"redir" 	=> '', 
		"username" 	=> "vidoomy_admin",
		"password" 	=> "Ericmolamazo2020+"
	);
	
	$json_encode = json_encode($post);
	$length = strlen($json_encode);
	
	$headers3 = array(
		'Accept: application/json, text/plain, */*',
		'Content-Type: application/json;charset=UTF-8',
		//'LKQD-Api-Version: 88',
		'Origin: https://ui.lkqd.com',
		'Referer: https://ui.lkqd.com/login',
		'Sec-Fetch-Mode: cors',
		'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$url = 'https://console.appnexus.com/login';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers3);
	curl_setopt($ch, CURLOPT_POST, 1);
	//curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$json_encode);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	$result = curl_exec($ch);
	//$information = curl_getinfo($ch);
	curl_close($ch); 
	
	//print_r($information);
	//echo $result;
}

function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}