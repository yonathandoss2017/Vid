<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('/var/www/html/login/config.php');
	require('/var/www/html/login/constantes.php');
	require('/var/www/html/login/db.php');
	require('/var/www/html/login/common.lib.php');
	require('/var/www/html/login/admin/libs/display.lib.php');
	
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	/*
	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	*/
	
	$dbuser2 = "root";
	$dbpass2 = "Jz8eDbamcNx3TskWzrjzH7g";
	$dbhost2 = "vidoomy-production.cpijmqdfbof9.eu-west-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');

function getOldCountryId($NewId){
	global $db, $db2;
	
	$sql = "SELECT iso FROM country WHERE id = '$NewId' LIMIT 1";
	$ISO = $db2->getOne($sql);
	
	$sql = "SELECT id FROM countries WHERE country_code = '$ISO' LIMIT 1";
	$NewCC = intval($db->getOne($sql));
	if($NewCC == 0){
		$NewCC = 999;
	}
	return $NewCC;
}	
	
	$sql = "SELECT user.email, user.username, user.password, user.name, user.last_name, publisher.* FROM user 
	INNER JOIN publisher ON user.id = publisher.user_id
	WHERE user.id >= 10000 AND roles LIKE '%\"ROLE_PUBLISHER\"%'";
	$query2 = $db2->query($sql);
	if($db2->num_rows($query2) > 0){
		while($U = $db2->fetch_array($query2)){
			//print_r($U );
			$idUser = $U['user_id'];
			
			$sql = "SELECT COUNT(*) FROM users WHERE id = '$idUser' LIMIT 1";		
			if($db->getOne($sql) == 0){
				
				$Date = date('Y-m-d');
				$Time = time();
				
				$User = $U['username'];
				$Email = $U['email'];
				$Pass = $U['password'];
				$Name = $U['name'];
				$Last = $U['last_name'];
				
				$Country = getOldCountryId($U['country_id']);
				$Currency = $U['currency_id'];
				$BCurrency = $U['bank_currency_id'];
				$BCountry = getOldCountryId($U['bank_country_id']);
				$AccM = $U['account_manager_id'];
				$Phone = $U['phone'];
				$Mobile = $U['mobile'];
				$WA = $U['whatsapp'];
				$Skype = $U['skype'];
				$Fiscal = $U['fiscalid'];
				if($U['fiscal_status'] == 1){
					$FS = 2;
				}elseif($U['fiscal_status'] == 2){
					$FS = 1;
				}else{
					$FS = 0;
				}
				$Company = $U['company'];
				$Province = $U['province'];
				$City = $U['city'];
				$CP = $U['zipcode'];
				$Address = $U['address'];
				$PT = $U['payment_type'];
				$Account = $U['account'];
				$BN = $U['bank_name'];
				$BAd = $U['bank_address'];
				$IBAN = $U['iban'];
				$SWIFT = $U['swift'];
				$Amount = $U['amount'];
				$NetTerms = $U['net_terms'];
				$LKQDID = $U['lkqdid'];
				$Nick = $U['nickname'];
				$Stats = $U['stats'];
				
				
				$sql = "INSERT INTO users (id, user, password, email, name, lastname, phone, movil, whatsapp, sykpe, ef, nifcif, company, country, province, city, cp, address, paymenttype, account, bankname, bankcountry, bankaddress, iban, netterms, amount, swift, currency, LKQD_id, SS_id, lastlogin, lastinvoice, image, remember, showi, AccM, type, time, date, enable_new) 
							VALUES ('$idUser','$User', '$Pass','$Email', '$Name', '$Last', '$Phone', '$Mobile', '$WA', '$Skype', '$FS', '$Fiscal', '$Company', '$Country', '$Province', '$City', '$CP', '$Address', '$PT', '$Account', '$BN', '$BCountry', '$BAd', '$IBAN', '$NetTerms', '$Amount', '$SWIFT', '$Currency', '$LKQDID', '', '0', '', '', '0', '$Stats', '$AccM', 0, '$Time', '$Date', 1)";
				//echo "<br/>";
				$db->query($sql);
				
			}
		}
	}
	
	
	$sql = "SELECT publisher.user_id, website.* FROM website
	INNER JOIN publisher ON publisher.id = website.publisher_id 
	WHERE website.id >= 10000";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($S = $db2->fetch_array($query)){
			
			$idSite = $S['id'];
			
			$sql = "SELECT COUNT(*) FROM sites WHERE id = '$idSite' LIMIT 1";		
			if($db->getOne($sql) == 0){
				
				$Date = date('Y-m-d');
				$Time = time();
				
				$SiteName = $S['sitename'];
				$URL = $S['url'];
				$Filename = $S['filename'];
				$idUser = $S['user_id'];
				$Category = $S['content_type'];
				
				$sql = "INSERT INTO sites 
				(id, idUser, sitename, siteurl, category, image, filename, eric, time, date) 
				VALUES 
				('$idSite', '$idUser', '$SiteName', '$URL','$Category', '', '$Filename', '1', '$Time', '$Date')";
				$db->query($sql);
				
				newGenerateJS($idSite);
			}
		}
	}
	
	
	$sql = "SELECT * FROM website_zone WHERE id >= 10000";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($Z = $db2->fetch_array($query)){
			
			$idZone = $Z['id'];
			
			$sql = "SELECT COUNT(*) FROM supplytag WHERE id = '$idZone' LIMIT 1";		
			if($db->getOne($sql) == 0){
				
				$Date = date('Y-m-d');
				$Time = time();
				
				$TagName = $Z['name'];
				$idTag = $Z['zone_id'];
				$idSite = $Z['website_id'];
				$Platform = $Z['platform'];
				
				$sql = "SELECT idUser FROM sites WHERE id = '$idSite'";
				$idUser = $db->getOne($sql);
				
				$sql = "INSERT INTO supplytag 
				(id, idUser, idPlatform, idSite, PlatformType, idTag, TagName, RevenueType, Revenue, Old, time, date) 
				VALUES 
				('$idZone', '$idUser', 1, '$idSite', '$Platform', '$idTag', '$TagName', '0', '0', '0', '$Time', '$Date')";
				$db->query($sql);
			}
		}
	}
	
	
	
	$DateTime = date('Y-m-d H:i:s', time() - 300);
	
	$sql = "SELECT publisher.user_id, website.* FROM website
	INNER JOIN publisher ON publisher.id = website.publisher_id 
	WHERE website.updated_at >= '$DateTime'";

	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($S = $db2->fetch_array($query)){
			$idSite = $S['id'];
			$UA = $S['updated_at'];
			
			$sql = "SELECT updated FROM sites WHERE id = '$idSite' LIMIT 1";
			$MyUpdated = $db->getOne($sql);
			
			if($MyUpdated != $UA){
			
				$Sitename = $S['sitename'];
				$URL = $S['url'];
				$Cat = $S['content_type'];
				$Test = $S['is_test_mode'];
				
				$sql = "UPDATE sites SET sitename = '$Sitename', siteurl = '$URL', category = '$Cat', test = '$Test', updated = '$UA' WHERE id = '$idSite' LIMIT 1";
				$db->query($sql);
				
				if($S['status'] == 1){
					$sql = "UPDATE sites SET eric = 2 WHERE id = '$idSite' LIMIT 1";
					$db->query($sql);
				}elseif($S['status'] == 4 && $S['rejection_reason'] != ''){
					$sql = "UPDATE sites SET eric = 3 WHERE id = '$idSite' LIMIT 1";
					$db->query($sql);
					
					$Message = $S['rejection_reason'];
					$sql = "INSERT INTO rejected_sites (idSite, Reason) VALUES ($idSite, '$Message')";
					$db->query($sql);
				}elseif($S['status'] == 3){
					$sql = "UPDATE sites SET deleted = 1 WHERE id = '$idSite' LIMIT 1";
					$db->query($sql);
				}
				
				newGenerateJS($idSite);
			}
		}
	}
	
	$DateTime = date('Y-m-d H:i:s', time() - 300);
	
	$sql = "SELECT * FROM ad WHERE updated_at >= '$DateTime' AND status = 1";

	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($A = $db2->fetch_array($query)){
			$idAd = $A['id'];
			$UA = $A['updated_at'];
			
			$sql = "SELECT COUNT(*) FROM ads WHERE id = '$idAd' LIMIT 1";
			if($db->getOne($sql) > 0){
				$sql = "SELECT updated FROM ads WHERE id = '$idAd' LIMIT 1";
				$MyUpdated = $db->getOne($sql);
				
				if($MyUpdated != $UA){
					$idSite = $A['website_id'];
					
					$divID = $A['divid'];
					$Width = $A['width'];
					$Height = $A['height'];
					$Close = $A['close'];
					$DFP = $A['dfp'];
					$Override = $A['override'];
					$HeightA = $A['height_a'];
					$SPosition = $A['sposition'];
					$CCode = mysqli_real_escape_string($db->link, $A['ccode']);
					$lkqdid = $A['lkqdid'];
					
					$sql = "UPDATE ads SET divID = '$divID', Width = '$Width', Height = '$Height', Close = '$Close', DFP = '$DFP', Override = '$Override', HeightA = '$HeightA', SPosition = '$SPosition', idLKQD = '$lkqdid', CCode = '$CCode', updated = '$UA' WHERE id = '$idAd' LIMIT 1";
					$db->query($sql);
					
					newGenerateJS($idSite);
				}
			}
		}
	}
	
	/*
	$DateTime = date('Y-m-d H:i:s', time() - 300);
	
	echo $sql = "SELECT * FROM website_zone WHERE updated_at >= '$DateTime'";
	$query = $db2->query($sql);
	echo 'AAA';
	if($db2->num_rows($query) > 0){
		echo 'BBB';
		while($Z = $db2->fetch_array($query)){
			$idZone = $Z['id'];
			
			$UA = $Z['updated_at'];
			
			$sql = "SELECT updated FROM supplytag WHERE id = '$idZone' LIMIT 1";
			$MyUpdated = $db->getOne($sql);
			
			if($MyUpdated != $UA){
				
				$TagName = $Z['name'];
				$idTag = $Z['zone_id'];
				$idSite = $Z['website_id'];
				$Platform = $Z['platform'];
				
				$sql = "UPDATE supplytag SET PlatformType = '$Platform', idTag = '$idTag', TagName = '$TagName', updated = '$UA' WHERE id = '$idZone' LIMIT 1";
				$db->query($sql);
				
			}
		}
	}*/

	$sql = "SELECT * FROM ad WHERE id >= 12000 AND status = 1";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($Ad = $db2->fetch_array($query)){
			
			$idAd = $Ad['id'];
			
			$sql = "SELECT COUNT(*) FROM ads WHERE id = '$idAd' LIMIT 1";		
			if($db->getOne($sql) == 0){
				
				$Date = date('Y-m-d');
				$Time = time();
				
				$idAd = $Ad['id'];
				$idSite = $Ad['website_id'];
				$idLkqd = $Ad['lkqdid'];
				$divID = $Ad['divid'];
				$Type = $Ad['ad_type_id'];
				if($Type > 4){
					$Type = 100;
				}
				$Width = $Ad['width'];
				$Height = $Ad['height'];
				$Close = $Ad['close'];
				$DFP = $Ad['dfp'];
				$Override = $Ad['override'];
				$AA = $Ad['height_a'];
				$Spos = $Ad['sposition'];
				//$CCode = $Ad['ccode'];
				$CCode = mysqli_real_escape_string($db->link, $Ad['ccode']);
				
				$sql = "INSERT INTO ads 
				(id, idSite, idSCode, idLKQD, divID, Type, Width, Height, Close, DFP, Override, HeightA, SPosition, CCode, Time, Date) 
				VALUES 
				('$idAd', '$idSite','0','$idLkqd','$divID','$Type','$Width','$Height','$Close','$DFP','$Override','$AA','$Spos',\"$CCode\",'$Time','$Date')";
				$db->query($sql);
				
				newGenerateJS($idSite);
			}
		}
	}
	
	$sql = "SELECT * FROM ad WHERE id >= 1000 AND status = 3";
	$query = $db2->query($sql);
	if($db2->num_rows($query) > 0){
		while($Ad = $db2->fetch_array($query)){
			
			$idAd = $Ad['id'];
			$idSite = $Ad['website_id'];
			
			$sql = "SELECT COUNT(*) FROM ads WHERE id = '$idAd' LIMIT 1";		
			if($db->getOne($sql) > 0){
				
				//echo "AD $idAd DELETED; Website: $idSite \n";
				
				$sql = "DELETE FROM ads WHERE id = '$idAd' LIMIT 1";
				$db->query($sql);
				
				newGenerateJS($idSite);
			}
		}
	}
	
	
	$sql = "SELECT user.email, user.username, user.password, user.name, user.last_name, user.updated_at, publisher.* FROM user 
	INNER JOIN publisher ON user.id = publisher.user_id
	WHERE user.updated_at >= '2020-01-01 00:00:00' AND roles LIKE '%\"ROLE_PUBLISHER\"%'";
	$query2 = $db2->query($sql);
	if($db2->num_rows($query2) > 0){
		while($U = $db2->fetch_array($query2)){
			//print_r($U );
			$idUser = $U['user_id'];
			$Updated = $U['updated_at'];
			
			$sql = "SELECT COUNT(*) FROM users WHERE id = '$idUser' AND (updated < '$Updated' OR updated IS NULL) LIMIT 1";		
			//echo '<br/>';
			if($db->getOne($sql) != 0){
				
				$User = $U['username'];
				$Email = $U['email'];
				$Pass = $U['password'];
				$Name = $U['name'];
				$Last = $U['last_name'];
				
				$Country = getOldCountryId($U['country_id']);
				$Currency = $U['currency_id'];
				$BCurrency = $U['bank_currency_id'];
				$BCountry = getOldCountryId($U['bank_country_id']);
				$AccM = $U['account_manager_id'];
				$Phone = $U['phone'];
				$Mobile = $U['mobile'];
				$WA = $U['whatsapp'];
				$Skype = $U['skype'];
				$Fiscal = $U['fiscalid'];
				if($U['fiscal_status'] == 1){
					$FS = 2;
				}elseif($U['fiscal_status'] == 2){
					$FS = 1;
				}else{
					$FS = 0;
				}
				$Company = $U['company'];
				$Province = $U['province'];
				$City = $U['city'];
				$CP = $U['zipcode'];
				$Address = $U['address'];
				$PT = $U['payment_type'];
				$Account = $U['account'];
				$BN = $U['bank_name'];
				$BAd = $U['bank_address'];
				$IBAN = $U['iban'];
				$SWIFT = $U['swift'];
				$Amount = $U['amount'];
				$NetTerms = $U['net_terms'];
				$LKQDID = $U['lkqdid'];
				$Nick = $U['nickname'];
				$Stats = $U['stats'];
				
				
				$sql = "UPDATE users SET password = '$Pass', email = '$Email', name = '$Name', lastname = '$Last', phone = '$Phone', movil = '$Mobile', whatsapp = '$WA', sykpe = '$Skype', ef = '$FS', nifcif = '$Fiscal', 
				company = '$Company', country = '$Country', province = '$Province', city = '$City', cp = '$CP', address = '$Address', paymenttype = '$PT', account = '$Account', bankname = '$BN', bankcountry = '$BCountry', 
				bankaddress = '$BAd', iban = '$IBAN', netterms = '$NetTerms', amount = '$Amount', swift = '$SWIFT', LKQD_id = '$LKQDID',  showi = '$Stats', AccM = '$AccM', updated = '$Updated' WHERE id = '$idUser' LIMIT 1";
				//echo "$sql <br/>";
				$db->query($sql);
				
			}
		}
	}
	
	
	
	
	