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
	
	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
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
				echo "$sql <br/>";
				$db->query($sql);
				
			}
		}
	}