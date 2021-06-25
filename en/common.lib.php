<?php
function my_clean($string, $html=FALSE) {
    $string=(get_magic_quotes_gpc()==0) ? addslashes($string) : $string;
    $string=trim($string);
	$string=($html==TRUE) ? $string : htmlspecialchars($string);
	return $string;
}
function convertirModRewrite($texto){
	$url = strtolower($texto);
	$url = str_replace("á","a",$url);
	$url = str_replace("é","e",$url);
	$url = str_replace("í","i",$url);
	$url = str_replace("ó","o",$url);
	$url = str_replace("ú","u",$url);
	$url = str_replace("ñ","n",$url);
	$find = array(' ',
	'&',
	'\r\n',
	'\n',
	'+');
	$url = str_replace ($find, '-', $url);
	$find = array('/[^a-z0-9\-<>]/',
	'/[\-]+/',
	'/<[^>]*>/');
	$repl = array('',
	'-',
	'');
	$url = preg_replace ($find, $repl, $url); 
	return $url;
}
function check($valor){
	if($valor==1){return "checked";}
}
function get_ext($key) { 
	$key=strtolower(substr(strrchr($key, "."), 1));
	$key=str_replace("jpeg","jpg",$key);
	return $key;
}
function cortarTexto($texto,$largo){
	if(strlen($texto)>$largo){
		$array = explode(' ',$texto);
		$longitud = 0;
		$texto_salida = '';
		foreach($array as $palabra){
			$longitud += strlen($palabra) + 1;
			if($longitud<$largo){
				$texto_salida .= $palabra . " ";
			}
		}
		$texto = trim($texto_salida) . '...';
	}
	return $texto;
}
function check_mail($mail){
	if ((strlen($mail) >= 6) && (substr_count($mail,"@") == 1) && (substr($mail,0,1) != "@") && (substr($mail,strlen($mail)-1,1) != "@")){
		if (substr_count($mail,".")>= 1){
			$term_dom = substr(strrchr ($mail, '.'),1);
			if (strlen($term_dom)>1 && strlen($term_dom)<5 && (!strstr($term_dom,"@")) ){
				$antes_dom = substr($mail,0,strlen($mail) - strlen($term_dom) - 1);
				$caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1);
				if ($caracter_ult != "@" && $caracter_ult != "."){
					return true;
				}else{return false;}
			}else{return false;}
		}else{return false;}
	}else{return false;}
}
function check_usuario($string) {
	$permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	for ($i=0; $i<strlen($string); $i++){
		if (strpos($permitidos, substr($string,$i,1))===false){
		return false;}
	}return true;
}
function generaClave($longitud){
	$password = "";
	if(!is_numeric($longitud) || $longitud <= 0){
		$longitud = 8;
	}
	if($longitud > 32){
		$longitud = 32;
	}
	$caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	mt_srand(microtime() * 1000000);
	for($i = 0; $i < $longitud; $i++){
		$key = mt_rand(0,strlen($caracteres)-1);
		$password = $password . $caracteres{$key};
	}
	return $password;
}
function send_mail($mail, $titulo, $body, $from = 'noresponder@tudescargadirecta.com'){
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-type: text/html;charset=ISO-8859-9\n";
	$headers .= "FROM: $from\n";
	if(mail($mail, $titulo, $body, $headers)){
		return true;
	}else{
		return false;
	}
}
function redirect($url)
{
    $string = '<script type="text/javascript">';
    $string .= 'window.location = "' . $url . '"';
    $string .= '</script>';

    echo $string;
}
function readmonth($m){
	$mm = intval($m);
	$AMonth = array(
		1 => 'January',
		2 => 'February',
		3 => 'March',
		4 => 'April',
		5 => 'May',
		6 => 'June',
		7 => 'July',
		8 => 'August',
		9 => 'September',
		10 => 'October',
		11 => 'November',
		12 => 'December'
	);
	return $AMonth[$mm];
}
function correctCurrency($value, $currency = 1){
	if($currency == 1){
		return $value;
	}else{
		if($value > 0){
			$value = $value - ($value * 20 / 100);
		}
		return $value;
	}
}

function generateJS($idSite){
	global $db;
	
	$sql = "SELECT * FROM " . ADS . " WHERE idSite = '$idSite' ";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		
		$sql = "SELECT filename FROM " . SITES . " WHERE id = '$idSite' LIMIT 1";
		$Filename = $db->getOne($sql);
		
		$arFilename = explode('/',$Filename);
		$Filename = $arFilename[3];
		
		$NewCode = '';
		
		while($Ads = $db->fetch_array($query)){
			$Type = $Ads['Type'];
			
			if($Type != 5){
				$sql = "SELECT Code FROM " . ADSCODES . " WHERE Type = '$Type' LIMIT 1";
				$Code = $db->getOne($sql);
				
				$Code = str_replace('#IDLKQD',$Ads['idLKQD'],$Code);
				$Code = str_replace('#divID',$Ads['divID'],$Code);
				$Code = str_replace('#Width',$Ads['Width'],$Code);
				$Code = str_replace('#Height',$Ads['Height'],$Code);
				if($Ads['Override'] == 1){ $OverrideTF = 'true'; } else { $OverrideTF = 'false'; }
				$Code = str_replace('#Override',$OverrideTF,$Code);
				if($Ads['DFP'] == 1){ $DFPTF = 'true'; } else { $DFPTF = 'false'; }
				$Code = str_replace('#DFP',$DFPTF,$Code);
				if($Ads['SPosition'] == 1){ $SposTF = 'right'; } else { $SposTF = 'left'; }
				$Code = str_replace('#Spos',$SposTF,$Code);
				if($Ads['Close'] == 1){ $CloseTF = 'true'; } else { $CloseTF = 'false'; }
				$Code = str_replace('#Close',$CloseTF,$Code);
				if($Ads['HeightA'] == 0){
					$Code = str_replace('#AA','',$Code);
				} else {
					$Code = str_replace('#AA',"\nbottomPadding: ".$Ads['HeightA'].",",$Code);
				}
			}else{
				$Code = "// custome code \n\n" . $Ads['CCode'];
			}
			
			$NewCode .= $Code . "\n\n";
		}
		//echo "../../Vidoomy/ads/$Filename";
		$myfile = fopen("/var/www/html/ads/$Filename", "w") or die("Unable to open file!");
		//$myfile = fopen("ads/$Filename", "w") or die("Unable to open file!");
		fwrite($myfile, $NewCode);
		fclose($myfile);
		
		$mem_var = new Memcached();
		$mem_var->addServer("localhost", 11211);
		
		$mem_var->set("/$Filename", $NewCode, 21600);
	}
}

function generateJSDouble($idSite, $TakeOut = false, $Sec = 5, $Rep = 1){
	global $db;
	
	$sql = "SELECT * FROM " . ADS . " WHERE idSite = '$idSite' ";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		
		$sql = "SELECT filename FROM " . SITES . " WHERE id = '$idSite' LIMIT 1";
		$Filename = $db->getOne($sql);
		
		$arFilename = explode('/',$Filename);
		echo $Filename = $arFilename[3];
		echo '<br>';
		
		$NewCode = '';
		
		while($Ads = $db->fetch_array($query)){
			$Type = $Ads['Type'];
			
			if($Type != 5){
				if($TakeOut === false){
					$Type = $Type + 4;
				}
				
				$sql = "SELECT Code FROM " . ADSCODES . " WHERE Type = '$Type' LIMIT 1";
				$Code = $db->getOne($sql);
				
				$Code = str_replace('#IDLKQD',$Ads['idLKQD'],$Code);
				$Code = str_replace('#divID',$Ads['divID'],$Code);
				$Code = str_replace('#Width',$Ads['Width'],$Code);
				$Code = str_replace('#Height',$Ads['Height'],$Code);
				$Code = str_replace('#SiteID',$Ads['id'],$Code);
				$Code = str_replace('#Sec',$Sec,$Code);
				$Code = str_replace('#Rep',$Rep,$Code);
				if($Ads['Override'] == 1){ $OverrideTF = 'true'; } else { $OverrideTF = 'false'; }
				$Code = str_replace('#Override',$OverrideTF,$Code);
				if($Ads['DFP'] == 1){ $DFPTF = 'true'; } else { $DFPTF = 'false'; }
				$Code = str_replace('#DFP',$DFPTF,$Code);
				if($Ads['SPosition'] == 1){ $SposTF = 'right'; } else { $SposTF = 'left'; }
				$Code = str_replace('#Spos',$SposTF,$Code);
				if($Ads['Close'] == 1){ $CloseTF = 'true'; } else { $CloseTF = 'false'; }
				$Code = str_replace('#Close',$CloseTF,$Code);
				if($Ads['HeightA'] == 0){
					$Code = str_replace('#AA','',$Code);
				} else {
					$Code = str_replace('#AA',"\nbottomPadding: ".$Ads['HeightA'].",",$Code);
				}
			}else{
				if($TakeOut){
					$NCode = $Ads['CCode'];
				}else{
					$NCode = 'var counter#SiteID = #Rep;
function secondPlayer#SiteID(){
	
counter#SiteID--;
if(counter#SiteID >= 0){' . $Ads['CCode'] . '}
}
secondPlayer#SiteID();';
				}
				
				if(strpos($NCode, 'AdViewable') != false){
					$NCode = str_replace("AdViewable');","AdViewable');\n    lkqdVPAID.subscribe(function() { setTimeout(function() { secondPlayer#SiteID(); }, #Sec000); }, 'AdStopped');", $NCode);
				}else{
					$NCode = str_replace("'AdLoaded');", "'AdLoaded');\n    lkqdVPAID.subscribe(function() { setTimeout(function() { secondPlayer#SiteID(); }, #Sec000); }, 'AdStopped');", $NCode);
				}
				
				$NCode = str_replace('#SiteID',$Ads['id'],$NCode);
				$NCode = str_replace('#Sec',$Sec,$NCode);
				$NCode = str_replace('#Rep',$Rep,$NCode);
				
				$Code = "// custome code \n\n" . $NCode;
			}
			
			$NewCode .= $Code . "\n\n";
		}
		//echo "../../Vidoomy/ads/$Filename";
		$myfile = fopen("/var/www/html/ads/$Filename", "w");
		//$myfile = fopen("ads/$Filename", "w") or die("Unable to open file!");
		fwrite($myfile, $NewCode);
		fclose($myfile);
	}
}

function getAdsTxt($url){
	$agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_VERBOSE, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); 
	curl_setopt($ch, CURLOPT_TIMEOUT, 8);	
	curl_setopt($ch, CURLOPT_COOKIEJAR, '/var/www/html/login/c.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, '/var/www/html/login/c.txt');
	$result = curl_exec($ch);
	
	if($result){
		if (stripos($result, 'Not Found') === false) {
			if (stripos($result, '301 Moved Permanently') === false) {
				return $result;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}else{
		return false;
	}
}

function domainToUrl($Url){
	if( substr($Url, 0, 4) == 'http' ){
		$NewUrl = $Url;
	}else{
		$NewUrl = 'http://' . $Url;
	} 
	
	return $NewUrl;
}

function urlToAdstxt($Url){
	if( substr($Url, 0, 4) == 'http' ){
		$NewUrl = $Url;
	}else{
		$NewUrl = 'http://' . $Url;
	}
			
	if( substr($Url, -1) == '/' ){
		$NewUrl = $NewUrl . 'ads.txt';
	}else{
		$NewUrl = $NewUrl . '/ads.txt';
	}
	return $NewUrl;
}

function getAdsTxtLine($NLine, $idUser){
	global $db;
	$sql = "SELECT LKQD_id FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
	$idLkqd = $db->getOne($sql);
	$sql = "SELECT LineTxt FROM " . ADSTXT . " WHERE id = '$NLine' LIMIT 1";
	$LineTxt = $db->getOne($sql);
	$LineTxt = str_replace('{LKQDID}', $idLkqd, $LineTxt);
	return $LineTxt;
}

function emailAdsTxt($Estado, $idSite, $NewLines = false, $RemovedLines = false){
	global $db, $mail;
	
	//$Bbc = "eric.raventos@vidoomy.com, marcos.cuesta@vidoomy.com";
	//$Bbc = "";
	
	$sql = "SELECT * FROM " . SITES . " WHERE id = '$idSite' LIMIT 1";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		$Site = $db->fetch_array($query);
		$idUser = $Site['idUser'];
		$SiteName = $Site['sitename'];
		
		$sql = "SELECT user FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
		$Publisher = $db->getOne($sql);
	
		$sql = "SELECT AccM FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
		$idAccM = $db->getOne($sql);
		
		if($idAccM > 0){
		
			$sql = "SELECT Email FROM " . ACC_MANAGERS . " WHERE id = '$idAccM' LIMIT 1";
			$Email = $db->getOne($sql);
			//$Email = "federico.izuel@vidoomy.com";
			$to = $Email;
			
			$sql = "SELECT Name FROM " . ACC_MANAGERS . " WHERE id = '$idAccM' LIMIT 1";
			$PubManager = $db->getOne($sql);
		
		}else{
			$to = "federico.izuel@vidoomy.com";
		}
		
		if($Estado == 2){
			$subject = $SiteName . ': Ads.txt no encontrado';
			
			$newmessage = "<p>Quiero decirte que archivo Ads.txt en el sitio <strong>$SiteName</strong> del Publisher \"<strong>$Publisher</strong>\" no pudo ser localizado.<br /><br />Saludos!</p>";
		}elseif($Estado == 1){
			$subject = $SiteName . ': Ads.txt incompleto';
			
			$newmessage = "<p>Quiero decirte que el Publisher \"<strong>$Publisher</strong>\" ha actualizado el archivo Ads.txt en su sitio en el sitio <strong>$SiteName</strong>.</p>";
			if($NewLines !== false && $NewLines != ''){
				$newmessage .= "<p>Líneas agregadas:<br/>".nl2br($NewLines)."</p>";
			}
			if($RemovedLines && $RemovedLines != ''){
				$newmessage .= "<p>Líneas faltantes:<br/>".nl2br($RemovedLines)."</p>";
			}
			$newmessage .= "<p>Saludos!</p>";
		}else{
			$subject = $SiteName . ': Ads.txt subido correctamente';
			
			$newmessage = "<p>Quiero decirte que el Publisher \"<strong>$Publisher</strong>\" ha actualizado el archivo Ads.txt en su sitio <strong>$SiteName</strong> y ha quedado completo.<br /><br />Felicidades!</p>";
		}
		
		/*
		$headers = "From: info@vidoomy.com\r\n";
		$headers .= "Reply-To: info@vidoomy.com\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		$headers .= 'Bcc: ' . $Bbc;
		*/						
		$message = '<html><body>';
		$message .= '<img src="http://login.vidoomy.com/img/vidoomy-logo.png" /><br /><br /><br />';
		$message .= "<p>Hola $PubManager,</p>";
	
		$message .= $newmessage;
									
		$message .= "</body></html>";
		
		/*
		mail($to, $subject, $message, $headers);
		*/


		
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
		$mail->SMTPDebug = 2;
		$mail->Debugoutput = 'html';
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 465;
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Username = "federico.izuel@vidoomy.com";
		$mail->Password = "Vidoomy4231";
		$mail->setFrom('info@vidoomy.com', 'Vidoomy');
		$mail->addReplyTo('info@vidoomy.com', 'Vidoomy');
		$mail->addAddress('federicoizuel@gmail.com', 'Federico Izuel');
		$mail->Subject = $subject;
		$mail->msgHTML($message);
		//$mail->AltBody = 'This is a plain-text message body';
		//Attach an image file
		//$mail->addAttachment('img/vidoomy-logo.png');
		//send the message, check for errors
		if (!$mail->send()) {
		    $myfile = fopen("../../errorlog/mail.txt", "w") or die("Unable to open file!");
			fwrite($myfile, "Mailer Error: " . $mail->ErrorInfo);
			fclose($myfile);
		} else {
		    $myfile = fopen("../../errorlog/mail.txt", "w") or die("Unable to open file!");
		    fwrite($myfile, "Message sent!");
			fclose($myfile);
		}
	
	}
}


function checkAdsTxt($idSite, $SiteUrl = ''){
	global $db;
	if($SiteUrl == ''){
		$sql = "SELECT siteurl FROM " . SITES . " WHERE id = '$idSite' LIMIT 1";
		$SiteUrl = $db->getOne($sql);
	}
	$Url = urlToAdstxt($SiteUrl);
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
	//echo 'A' . $AdsText . 'B';						
	if($NotFound !== true){
		//$idUser = $user;
		//$idSite = $siteId;
						
		$Coma = '';
		$N = 0;
		$Mlines = '';
		$Complete = true;
							
		$sql = "SELECT * FROM " . ADSTXT . " ORDER BY id ASC";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			while($Line = $db->fetch_array($query)){
				$N++;
											
				if($Line['id'] == 1){
					$LineTxt = 'vidoomy.com';
				}else{
					$LineTxt = $Line['LineTxt'];
				}
			
			
				//$string = ('/\s+/', '', $string);

				if (stripos(preg_replace('/\s+/', '', $AdsText), preg_replace('/\s+/', '', $LineTxt)) !== false) {
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
			$AdsTxtState = 0;
		}else{
			$sql = "UPDATE " . SITES . " SET adstxt = 1, mlines = '$Mlines' WHERE id = '$idSite' LIMIT 1";
			$AdsTxtState = 1;
		}
	}else{
		$sql = "UPDATE " . SITES . " SET adstxt = 2, mlines = '' WHERE id = '$idSite' LIMIT 1";
		$AdsTxtState = 2;
	}
	$db->query($sql);
	return $AdsTxtState;
}


function totalViews($idSite){
	$jsonData = file_get_contents('http://pixel.vidoomy.com/viewsresults.php?siteid=' . $idSite);
	$countVis = 0;
	if($jsonData != 'null'){
		$siteData = json_decode($jsonData);
		if(count($siteData) > 0){
			foreach($siteData as $date => $views){
				$countVis += $views;
			}
		}
	}
	return $countVis;
}

function notifyUserAccountState($idUser, $UserState){
	global $db, $mail, $MailAprobado, $ApprovedDenied, $MailMotivos;
	$sql = "SELECT * FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		$UserData = $db->fetch_array($query);
		
		$UserName = $UserData['name'] . ' ' . $UserData['lastname'];
		$UserEmail = $UserData['email'];
		$UserLang = $UserData['lang'];
		
		if($UserLang != 'es'){
			$UserLang = 'en';
		}
		
		$AtLeastOne = false;
		$SitesState = '';
		$Ns = 0;
		$sql = "SELECT * FROM " . SITES . " WHERE idUser = '$idUser' ORDER BY aproved ASC, sitename ASC ";
		$query = $db->query($sql);
		if($db->num_rows($query) > 0){
			while($Site = $db->fetch_array($query)){
				$Ns++;
				if($Site['aproved'] != 0){
					$AtLeastOne = true;
					$SitesState .= $Site['siteurl'] . ': ' . $ApprovedDenied[2][$UserLang] . ': ' . $MailMotivos[$Site['aproved']][$UserLang] . '<br/>';
				}else{
					$SitesState .= $Site['siteurl'] . ': ' . $ApprovedDenied[1][$UserLang] . '<br/>';  
				}
			}
		}
		
		if($Ns > 1){
			$Txt = 'txts';
		}else{
			$Txt = 'txt';
		}
		
		if($UserState == 1){
			if($AtLeastOne){
				$Subject = $MailAprobado[3][$UserLang]['subject'];
				$Message = $MailAprobado[3][$UserLang][$Txt];
			}else{
				$Subject = $MailAprobado[1][$UserLang]['subject'];
				$Message = $MailAprobado[1][$UserLang][$Txt];
			}
		}else{
			$Subject = $MailAprobado[2][$UserLang]['subject'];
			$Message = $MailAprobado[2][$UserLang][$Txt];
		}
		
		$Message = str_replace('{User}', $UserName, $Message);
		$Message = str_replace('{Sites}', $SitesState, $Message);
		
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Debugoutput = 'html';
		
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 465;
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;
		$mail->Username = "alta@vidoomy.com";
		$mail->Password = "RegVidoom1-2";
		$mail->CharSet = 'UTF-8';
		$mail->setFrom('alta@vidoomy.com', 'Vidoomy');
		$mail->addReplyTo('alta@vidoomy.com', 'Vidoomy');
		$mail->addAddress($UserEmail, $UserName);
		
		$mail->AddCC('raquel.fernandez@vidoomy.com');
		$mail->AddBCC('javier.bejar@vidoomy.com');
		$mail->AddBCC('marcos.cuesta@vidoomy.com');
		$mail->AddBCC('federico.izuel@vidoomy.com');
		 
		
		$MailContent = file_get_contents('/var/www/html/site/slider/htmls/email2.html');
		$MailContent = str_replace('{{Txt3}}', $Message, $MailContent);
		$MailContent = str_replace('{{Txt2}}', '', $MailContent);
		$MailContent = str_replace('{{Txt1}}', '', $MailContent);
		$MailContent = str_replace('{{align}}', 'left', $MailContent);
		
		$mail->Subject = $Subject;
		$mail->msgHTML($MailContent);
		if (!$mail->send()) {
		    $myfile = fopen("../../errorlog/mail2.txt", "w") or die("Unable to open file!");
			fwrite($myfile, "Mailer Error: " . $mail->ErrorInfo);
			fclose($myfile);
		} else {
		    $myfile = fopen("../../errorlog/mail2.txt", "w") or die("Unable to open file!");
		    fwrite($myfile, "Message sent!");
			fclose($myfile);
		}
		
	}
}

?>