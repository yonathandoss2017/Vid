<?php	
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/db.php');
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	require('/var/www/html/login/admin/lkqdimport/common.php');
	
	$cookie_file = '/var/www/html/login/admin/lkqdimport/cookie.txt';
	
	
	$dbuser_adv_pre = "root";
	$dbpass_adv_pre = "Kw6tbHnTtukP3tV2pDqBs7xP6TG2DhFe";
	$dbhost_adv_pre = "aazw79txt1iy6x.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname_adv_pre = "vidoomy-advertisers-panel";
//	$db = new SQL($dbhost_adv_pre, $dbname_adv_pre, $dbuser_adv_pre, $dbpass_adv_pre);

		
	$dbuser_adv_prod = "root";
	$dbpass_adv_prod = "pthFTa8Lp25xs7Frkqgkz5HRebmwVGPY";
	$dbhost_adv_prod = "aa14extn6ty9ilx.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname_adv_prod = "vidoomy-advertisers-panel";
	$db = new SQL($dbhost_adv_prod, $dbname_adv_prod, $dbuser_adv_prod, $dbpass_adv_prod);

function notifyFailure($Log = ''){
	$mail = new PHPMailer;
								
	$mail->isSMTP();
	$mail->SMTPDebug = 0;
	$mail->Debugoutput = 'html';
	
	$mail->Host = 'smtp.gmail.com';
	$mail->Port = 465;
	$mail->SMTPSecure = 'ssl';
	$mail->SMTPAuth = true;
	$mail->Username = "notifysystem@vidoomy.net";
	$mail->Password = "NoTyFUCK05-1";
	$mail->CharSet = 'UTF-8';
	$mail->setFrom('notifysystem@vidoomy.net', 'Vidoomy');
	$mail->addReplyTo('notifysystem@vidoomy.net', 'Vidoomy');
	$mail->addAddress('federico.izuel@vidoomy.com');
	
	$mail->Subject = 'Video Upload FAIL (Creador de demos)';
	$mail->msgHTML(nl2br($Log));
	$mail->send();
}

function changeDemoStatus($ID, $newStatus, $SSDT = '', $SSMW = '', $CreativeId = ''){
	global $db;
	
	$UpdateSS = "";
	
	if($SSDT != ''){
		$UpdateSS .= ", supply_source_desktop = '$SSDT' ";
	}
	if($SSMW != ''){
		$UpdateSS .= ", supply_source_mobile = '$SSMW' ";
	}
	if($CreativeId != ''){
		$UpdateSS .= ", creative = '$CreativeId' ";
	}	
	
	$sql = "UPDATE demo SET status = $newStatus $UpdateSS WHERE id = $ID LIMIT 1";
//	exit(0);
	$db->query($sql);
	
	return true;
}

function uniqidReal($lenght = 13) {
    // uniqid gives 13 chars, but you could adjust it to your needs.
    if (function_exists("random_bytes")) {
        $bytes = random_bytes(ceil($lenght / 2));
    } elseif (function_exists("openssl_random_pseudo_bytes")) {
        $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    } else {
        throw new Exception("no cryptographically secure random function available");
    }
    return substr(bin2hex($bytes), 0, $lenght);
}
function build_data_files($boundary, $fields, $files){
    $data = '';
    $eol = "\r\n";

    $delimiter = '----WebKitFormBoundary' . $boundary;

    foreach ($fields as $name => $content) {
        $data .= "--" . $delimiter . $eol
            . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol
            . $content . $eol;
    }


    foreach ($files as $name => $content) {
	    $FileInfo = pathinfo($name);
	    
	    //echo "FileName:" . $FileInfo['basename'] . "\n";
	    
        $data .= "--" . $delimiter . $eol
            . 'Content-Disposition: form-data; name="file"; filename="' . $FileInfo['basename'] . '"' . $eol
            . 'Content-Type: video/mp4'.$eol
//            . 'Content-Transfer-Encoding: binary'.$eol
            ;

        $data .= $eol;
        $data .= $content . $eol;
    }
    $data .= "--" . $delimiter . "--".$eol;


    return $data;
}

function newCreative($Name){
	global $sessionId, $cookie_file;
	$uuid = gen_uuid();
	$fileDownloadToken = rand(100000,999999);
	
	$post = array(
		"demandPartnerId" => 39629,
		"mediaType" => 'video',
		"name" => $Name,
	);
	
	$json = json_encode($post);
	$url = 'https://api.lkqd.com/demand/creatives';
	
	$headers = array();
	$headers[] = 'authority: api.lkqd.com';
	$headers[] = 'accept: application/json, text/plain, */*';
	$headers[] = 'accept-encoding: gzip, deflate, br';
	$headers[] = 'accept-language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6';
	$headers[] = 'cache-control: no-cache';
	$headers[] = 'content-type: application/json;charset=UTF-8';
	$headers[] = 'origin: https://ui.lkqd.com';
	$headers[] = 'pragma: no-cache';
	$headers[] = 'referer: https://ui.lkqd.com/demand-partners/39629/new-creative';
	$headers[] = 'sec-fetch-dest: empty';
	$headers[] = 'sec-fetch-mode: cors';
	$headers[] = 'sec-fetch-site: same-site';
	$headers[] = 'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36';
	
	$ch = curl_init();
	curl_setopt_array($ch, array(
	    CURLOPT_URL => $url,
	    CURLOPT_CUSTOMREQUEST => 'OPTIONS',
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_HEADER => true,
	    CURLOPT_HTTPHEADER => $headers,
	    CURLOPT_NOBODY => true,
	    CURLOPT_VERBOSE => false,
	    CURLOPT_COOKIEJAR => $cookie_file,
	    CURLOPT_COOKIEFILE => $cookie_file
	));
	$r = curl_exec($ch);
	
	/*
	echo PHP_EOL.'Response Headers:'.PHP_EOL;
	print_r($r);
	*/
	curl_close($ch);
	//exit(0);
	
		
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    
	$result = curl_exec($ch);
	curl_close($ch);  
	
	$result = gzdecode($result);

	if(substr($result, 0, 4) == 'HTTP'){
		return false;
	}else{
		return json_decode($result);
	}
}

function uploadVideo($cId, $videoFile){
	global $sessionId, $cookie_file;
	$uuid = gen_uuid();
	$fileDownloadToken = rand(100000,999999);
	
	/*
	$post = array(
		'creativeId' 	=> $cId,
		'file'			=> $FileBody
	);
	
	$json = json_encode($post);
	*/
	$url = 'https://api.lkqd.com/demand/creatives/' . $cId . '/upload-video';
	
	$headers = array();
	$headers[0] = 'authority: api.lkqd.com';
	$headers[1] = 'accept: application/json, text/plain, */*';
	$headers[2] = 'accept-encoding: gzip, deflate, br';
	$headers[3] = 'accept-language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6';
	$headers[4] = 'cache-control: no-cache';
	$headers[5] = 'content-type: application/json;charset=UTF-8';
	$headers[6] = 'origin: https://ui.lkqd.com';
	$headers[7] = 'pragma: no-cache';
	$headers[8] = 'referer: https://ui.lkqd.com/demand-partners/39629/creatives/' . $cId;
	$headers[9] = 'sec-fetch-dest: empty';
	$headers[10] = 'sec-fetch-mode: cors';
	$headers[11] = 'sec-fetch-site: same-site';
	$headers[12] = 'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36';
	
	$ch = curl_init();
	curl_setopt_array($ch, array(
	    CURLOPT_URL => $url,
	    CURLOPT_CUSTOMREQUEST => 'OPTIONS',
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_HEADER => true,
	    CURLOPT_HTTPHEADER => $headers,
	    CURLOPT_NOBODY => true,
	    CURLOPT_VERBOSE => false,
	    CURLOPT_COOKIEJAR => $cookie_file,
	    CURLOPT_COOKIEFILE => $cookie_file
	));
	$r = curl_exec($ch);
	curl_close($ch);
	
	$fields = array("creativeId" => $cId);
	
	$filenames = array($videoFile);
	$files = array();
	foreach ($filenames as $f){
	   $files[$f] = file_get_contents($f);
	}
	
	//$boundary = uniqid();
	$boundary = uniqidReal(16);
	$delimiter = '----WebKitFormBoundary' . $boundary;
	
	$post_data = build_data_files($boundary, $fields, $files);
	
	//echo $post_data;
	//exit(0);

	
	$headers[5] = "content-type: multipart/form-data; boundary=" . $delimiter;
	$headers[13] = 'content-length: ' . strlen($post_data);
			
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    
	$result = curl_exec($ch);
	curl_close($ch);  
	
	$result = gzdecode($result);
	
	//echo $result;
}


function newDemandTag($Name){
	global $cookie_file;
		
	$url = 'https://ui-api.lkqd.com/tags';
	
	$headers = array();
	$headers[0] = 'authority: ui-api.lkqd.com';
	$headers[1] = 'accept: application/json, text/plain, */*';
	$headers[2] = 'accept-encoding: gzip, deflate, br';
	$headers[3] = 'accept-language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6';
	$headers[4] = 'cache-control: no-cache';
	$headers[5] = 'content-type: application/json;charset=UTF-8';
	$headers[6] = 'origin: https://ui.lkqd.com';
	$headers[7] = 'pragma: no-cache';
	$headers[8] = 'https://ui.lkqd.com/deals/989623';
	$headers[9] = 'sec-fetch-dest: empty';
	$headers[10] = 'sec-fetch-mode: cors';
	$headers[11] = 'sec-fetch-site: same-site';
	$headers[12] = 'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36';
	
	$ch = curl_init();
	curl_setopt_array($ch, array(
	    CURLOPT_URL => $url,
	    CURLOPT_CUSTOMREQUEST => 'OPTIONS',
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_HEADER => true,
	    CURLOPT_HTTPHEADER => $headers,
	    CURLOPT_NOBODY => true,
	    CURLOPT_VERBOSE => false,
	    CURLOPT_COOKIEJAR => $cookie_file,
	    CURLOPT_COOKIEFILE => $cookie_file
	));
	$r = curl_exec($ch);
	//exit(0);
	//echo $r;
	
	$URL = 'https://ui-api.lkqd.com/tags';
	//echo $URL . "\n";
	
	
	$JsonDec = json_decode(
		str_replace('{CTURL}', 'null',
		str_replace('{NAME}', $Name, file_get_contents('/var/www/html/login/admin/lkqdimport/new_tag.json')
		))
	);
	
	//print_r($JsonDec);
	//exit(0);

	$RequestPayloadJson = json_encode($JsonDec);
	//exit(0);
	
	$Headers = array(
		'accept: application/json, text/plain, */*',
		'accept-encoding: gzip, deflate, br',
		'cache-control: no-cache',
		'content-length: ' . strlen($RequestPayloadJson),
		'accept-language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6',
		'content-type: application/json;charset=UTF-8',
		'origin: https://ui.lkqd.com',
		'referer: https://ui.lkqd.com/deals/989623',
		'lkqd-api-version: 88',
		'sec-fetch-dest: empty',
		'sec-fetch-mode: cors',
		'sec-fetch-site: same-site',
		'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $RequestPayloadJson);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch); 
	
	$GzRes = gzdecode($result);
	$Data = json_decode($GzRes);
	
//	print_r($Data);
//	exit(0);
	file_put_contents('/var/www/html/login/admin/lkqdimport/demos_log/demandcreationresult.txt', $GzRes, FILE_APPEND);
	
	if($Data->status == 'success'){
		return $Data->data->tagId;
	}else{
		return false;
	}
}

function activateDemandTag($Name, $TagId, $CURL = ''){
	global $cookie_file;
		
	$url = 'https://ui-api.lkqd.com/tags';
	
	$headers = array();
	$headers[0] = 'authority: ui-api.lkqd.com';
	$headers[1] = 'accept: application/json, text/plain, */*';
	$headers[2] = 'accept-encoding: gzip, deflate, br';
	$headers[3] = 'accept-language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6';
	$headers[4] = 'cache-control: no-cache';
	$headers[5] = 'content-type: application/json;charset=UTF-8';
	$headers[6] = 'origin: https://ui.lkqd.com';
	$headers[7] = 'pragma: no-cache';
	$headers[8] = 'https://ui.lkqd.com/deals/989623';
	$headers[9] = 'sec-fetch-dest: empty';
	$headers[10] = 'sec-fetch-mode: cors';
	$headers[11] = 'sec-fetch-site: same-site';
	$headers[12] = 'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36';
	
	$ch = curl_init();
	curl_setopt_array($ch, array(
	    CURLOPT_URL => $url,
	    CURLOPT_CUSTOMREQUEST => 'OPTIONS',
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_HEADER => true,
	    CURLOPT_HTTPHEADER => $headers,
	    CURLOPT_NOBODY => true,
	    CURLOPT_VERBOSE => false,
	    CURLOPT_COOKIEJAR => $cookie_file,
	    CURLOPT_COOKIEFILE => $cookie_file
	));
	$r = curl_exec($ch);
	//exit(0);
	//echo $r;
	
	$URL = 'https://ui-api.lkqd.com/tags';
	//echo $URL . "\n";
	
	if($CURL != ''){
		$CURL = '"' . $CURL . '"';
	}else{
		$CURL = 'null';
	}
	
	$JsonDec = json_decode(
		str_replace('{TAGID}', $TagId,
		str_replace('{CTURL}', $CURL,
		str_replace('{NAME}', $Name, file_get_contents('/var/www/html/login/admin/lkqdimport/new_tag_active.json')
		)))
	);

	$RequestPayloadJson = json_encode($JsonDec);
	//print_r($JsonDec);
	//exit(0);
	
	$Headers = array(
		'accept: application/json, text/plain, */*',
		'accept-encoding: gzip, deflate, br',
		'cache-control: no-cache',
		'content-length: ' . strlen($RequestPayloadJson),
		'accept-language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6',
		'content-type: application/json;charset=UTF-8',
		'origin: https://ui.lkqd.com',
		'referer: https://ui.lkqd.com/deals/989623',
		'lkqd-api-version: 88',
		'sec-fetch-dest: empty',
		'sec-fetch-mode: cors',
		'sec-fetch-site: same-site',
		'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $RequestPayloadJson);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch); 
	
	$Data = json_decode(gzdecode($result));
	
	//print_r($Data);
	//exit(0);
	
	if($Data->status == 'success'){
		return true;
	}else{
		return false;
	}
}

function updateDemandTag($Name, $SsDt, $SsMMw, $CURL){
	global $cookie_file;
		
	$URL = "https://api.lkqd.com/supply-tags/find-by-id?siteId=$SsMMw";
	
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
	//exit(0);
	if(property_exists($Data, 'errorId')){
		return $Data->errorId;
	}
	
	$tagSiteAssociations = array();
	
	foreach($Data->associations as $D){
		if($D->tagId > 431566){
			$TagId = $D->tagId;
		}
	}
		
	$url = 'https://ui-api.lkqd.com/tags';
	
	$headers = array();
	$headers[0] = 'authority: ui-api.lkqd.com';
	$headers[1] = 'accept: application/json, text/plain, */*';
	$headers[2] = 'accept-encoding: gzip, deflate, br';
	$headers[3] = 'accept-language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6';
	$headers[4] = 'cache-control: no-cache';
	$headers[5] = 'content-type: application/json;charset=UTF-8';
	$headers[6] = 'origin: https://ui.lkqd.com';
	$headers[7] = 'pragma: no-cache';
	$headers[8] = 'https://ui.lkqd.com/deals/989623';
	$headers[9] = 'sec-fetch-dest: empty';
	$headers[10] = 'sec-fetch-mode: cors';
	$headers[11] = 'sec-fetch-site: same-site';
	$headers[12] = 'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36';
	
	$ch = curl_init();
	curl_setopt_array($ch, array(
	    CURLOPT_URL => $url,
	    CURLOPT_CUSTOMREQUEST => 'OPTIONS',
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_HEADER => true,
	    CURLOPT_HTTPHEADER => $headers,
	    CURLOPT_NOBODY => true,
	    CURLOPT_VERBOSE => false,
	    CURLOPT_COOKIEJAR => $cookie_file,
	    CURLOPT_COOKIEFILE => $cookie_file
	));
	$r = curl_exec($ch);
	//exit(0);
	//echo $r;
	
	$URL = 'https://ui-api.lkqd.com/tags';
	//echo $URL . "\n";
	
	if($CURL != ''){
		$CURL = '"' . $CURL . '"';
	}else{
		$CURL = 'null';
	}
	
	$JsonDec = json_decode(
		str_replace('{TAGID}', $TagId,
		str_replace('{CTURL}', $CURL,
		str_replace('{NAME}', $Name, file_get_contents('/var/www/html/login/admin/lkqdimport/new_tag_active.json')
		)))
	);

	$RequestPayloadJson = json_encode($JsonDec);
	//print_r($JsonDec);
	//exit(0);
	
	$Headers = array(
		'accept: application/json, text/plain, */*',
		'accept-encoding: gzip, deflate, br',
		'cache-control: no-cache',
		'content-length: ' . strlen($RequestPayloadJson),
		'accept-language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6',
		'content-type: application/json;charset=UTF-8',
		'origin: https://ui.lkqd.com',
		'referer: https://ui.lkqd.com/deals/989623',
		'lkqd-api-version: 88',
		'sec-fetch-dest: empty',
		'sec-fetch-mode: cors',
		'sec-fetch-site: same-site',
		'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $RequestPayloadJson);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch); 
	
	$GzRes = gzdecode($result);
	$Data = json_decode($GzRes);
	
	//print_r($Data);
	//exit(0);
	
	if($Data->status == 'success'){
		return true;
	}else{
		file_put_contents('/var/www/html/login/admin/lkqdimport/demos_log/failtoupdatedemandtag.txt', $GzRes, FILE_APPEND);
		return false;
	}
}

function associateDemandTagCreative($DemandTagId, $creativeId){
	global $cookie_file;
	
	$URL = 'https://api.lkqd.com/demand/creatives/tag-associations';
	
	$AssArray = array(
		"adds"	=> 	array(
			array(
				"tagId" => $DemandTagId,
				"creativeId" => $creativeId,
			),
		),
		"removes" => array(),
	);
	
	$RequestPayloadJson = json_encode($AssArray);
	
	//echo $RequestPayloadJson;
	//exit(0);
	
	$Headers = array(
		'accept: application/json, text/plain, */*',
		'accept-encoding: gzip, deflate, br',
		'accept-language: en-US,en;q=0.9,es;q=0.8,ca;q=0.7,pt;q=0.6',
		'cache-control: no-cache',
		'content-length: ' . strlen($RequestPayloadJson),
		'content-type: application/json;charset=UTF-8',
		'origin: https://ui.lkqd.com',
		'pragma: no-cache',
		'referer: https://ui.lkqd.com/deals/989623',
		'sec-fetch-dest: empty',
		'sec-fetch-mode: cors',
		'sec-fetch-site: same-site',
		'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.89 Safari/537.36',
	);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);
	//curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $RequestPayloadJson);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

	$result = curl_exec($ch);
	curl_close($ch); 
}


function newDemoSupplySource($Name, $TagId, $Env = 1, $Rev = 0, $Loop = 0, $debug = false){
	global $cookie_file;
	
	if($Env == 1){
		$environmentId = 3;
		$Name .= "_dt";
		$LKQDMPID = 431568;
	}else{
		$environmentId = 1;
		$Name .= "_mw";
		$LKQDMPID = 431566;
	}
	
	$URL = 'https://api.lkqd.com/supply/sources';
	
	$LastU = date('Y-m-d\TH:i:s.') . rand(100,999) . 'Z';

	$JsonDec = json_decode(
		str_replace('{TAGID}', $TagId, 
		str_replace('{ENV}', $environmentId, 
		str_replace('{LUAT}', $LastU,  
		str_replace('{LKQDMPID}', $LKQDMPID,  
		str_replace('{NAME}', $Name, file_get_contents('/var/www/html/login/admin/lkqdimport/new_supply_source.json') )))))
	);
	//print_r($JsonDec);
	//exit();
	
	$RequestPayloadJson = json_encode($JsonDec);
	
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
	//print_r($Data);
	
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
	
	
	$sql = "SELECT * FROM demo WHERE id = 51"; //status = 0 AND video != '' ORDER BY id ASC
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Demo = $db->fetch_array($query)){
			$SSDT = '';
			$SSMW = '';
			
			$ID = $Demo['id'];
			$NewVideoName = $Demo['url'];
			$CURL = $Demo['click_url'];
			//$videoFile = '/var/www/html/login/admin/lkqdimport/SUP_TEST.mov';
			$videoFile = $Demo['video'];
			$When = date('Y-m-d H:m:s');

//			$NewVideoName = "complete_creation_test_8_1dt";
//			$CURL = 'https://www.vidoomy.com/clickT';
			
			$Log = "|$When| New Video Upload - Name: $NewVideoName ID: $ID \n\n";
			
			//$newCreativeId = 25794;
			$newCreativeData = newCreative($NewVideoName);
			$newCreativeId = $newCreativeData->creativeId;
			
			$Log .= "|$When| New Creative ID: " . $newCreativeId . " \n";
			//print_r($newCreativeData);
			
			if($newCreativeId > 0){
				uploadVideo($newCreativeId, $videoFile); //
				$Log .= "|$When| Video Uploaded: $videoFile \n";
			}else{
				$Log .= "|$When| Fail Creative Creation \n";
				changeDemoStatus($ID, 4);
				notifyFailure($Log);
				exit();
			}
			
			//$newDemandTagId = 1039466;
			$newDemandTagId = newDemandTag($NewVideoName);
			$Log .= "|$When| Demand Tag ID: $newDemandTagId \n";
			
			if($newDemandTagId > 0){
				associateDemandTagCreative($newDemandTagId, $newCreativeId);
				$Log .= "|$When| Association Done: $newDemandTagId => $newCreativeId \n";
			}else{
				$Log .= "|$When| Fail Demand Tag Creation \n";
				notifyFailure($Log);
				changeDemoStatus($ID, 4);
				file_put_contents('/var/www/html/login/admin/lkqdimport/demos_log/' . $ID . '.txt', $Log, FILE_APPEND);
				exit();
			}
			
			if(activateDemandTag($NewVideoName, $newDemandTagId, $CURL)){
			
				for($I=1; $I <= 2; $I++){
					$Env = $I;
					
					if($Env == 1){
						$Log .= "|$When| New Desktop SS \n";
					}else{
						$Log .= "|$When| New MW SS \n";
					}
				
					$SupplySourceId = newDemoSupplySource($NewVideoName, $newDemandTagId, $Env);
					if($SupplySourceId > 0){
						if($Env == 1){
							$SSDT = $SupplySourceId;
						}else{
							$SSMW = $SupplySourceId;
						}
						
						$Log .= "|$When| $Env - New Supply Source ID: $SupplySourceId \n";
					}else{
						$Log .= "|$When| $Env - Fail New Supply Source \n";
						notifyFailure($Log);
						changeDemoStatus($ID, 4);
					}

					$Log .= "\n";
				}
				
			}else{
				$Log .= "|$When| $Env - Fail Demand Tag Activation \n";
				notifyFailure($Log);
				changeDemoStatus($ID, 4);
				file_put_contents('/var/www/html/login/admin/lkqdimport/demos_log/' . $ID . '.txt', $Log, FILE_APPEND);
				exit();
			}
		
			file_put_contents('/var/www/html/login/admin/lkqdimport/demos_log/' . $ID . '.txt', $Log, FILE_APPEND);
			
			changeDemoStatus($ID, 1, $SSDT, $SSMW, $newCreativeId);
		}
	}
	
	