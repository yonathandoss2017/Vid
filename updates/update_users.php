<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');

	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	mysqli_set_charset($db->link,'utf8');
	mysqli_set_charset($db2->link,'utf8');
	
	//$Day = date('Y-m-d', time() - (24 * 3600));
	//$Day = date('2019-11-04');

function getNewCountryId($OldId){
	global $db, $db2;
	
	$sql = "SELECT country_code FROM countries WHERE id = '$OldId' LIMIT 1";
	$ISO = $db->getOne($sql);
	
	$sql = "SELECT id FROM country WHERE iso = '$ISO' LIMIT 1";
	$NewCC = intval($db2->getOne($sql));
	if($NewCC == 0){
		$NewCC = 999;
	}
	return $NewCC;
}

function migrateSite($idSite){
	global $db, $db2;
	$sql = "SELECT * FROM " . SITES . " WHERE id = '$idSite' LIMIT 1";
	$query = $db->query($sql);
	$SiteData = $db->fetch_array($query);
	
	$idUser = $SiteData['idUser'];
	
	$sql = "SELECT AccM FROM users WHERE id = '$idUser'";
	$AccM = $db->getOne($sql);
	
	$sql = "SELECT id FROM publisher WHERE user_id = '$idUser'";
	//echo '<br/>';
	$idPublisher = $db2->getOne($sql);
	if($idPublisher > 0) {
	
		$SiteName = $SiteData['sitename'];
		$SiteURL = $SiteData['siteurl'];
		$TestMode = $SiteData['test'];
		$ContentType = $SiteData['category'];
		
		$FileName = $SiteData['filename'];
		
		if(intval($SiteData['time']) > 0){
			$DateTime = date('Y-m-d H:i:s', $SiteData['time']);
		}else{
			$DateTime = '2018-01-01 00:00:00';
		}
		
		if($SiteData['deleted'] == 1){
			$Status = 3;
		}elseif($SiteData['aproved'] > 0 || $SiteData['eric'] == 3){
			$Status = 4;
		}elseif($SiteData['eric'] == 1){
			$Status = 5;
		}else{
			$Status = 1;
		}
		
		$sql = "SELECT COUNT(*) FROM website WHERE id = '$idSite'";
		if($db2->getOne($sql) == 0){
			$sql = "INSERT INTO website (
				id, 
				created_by,
				sitename, 
				url, 
				filename, 
				status, 
				created_at, 
				publisher_id, 
				content_type,
				is_test_mode
			) 
			VALUES (
				$idSite,
				$idUser,
				'$SiteName',
				'$SiteURL',
				'$FileName',
				'$Status',
				'$DateTime',
				'$idPublisher',
				'$ContentType',
				'$TestMode'
			)";
			
			echo $sql;
			$db2->query($sql);
			
			$sql = "INSERT INTO account_manager_website (
				account_manager_id,
				website_id
			) 
			VALUES (
				$AccM,
				$idSite
			)";
			$db2->query($sql);
			
			echo $idSite . ' Sitio insertado<br/>';
		} else {
			$sql = "UPDATE website SET sitename = '', status = '$Status', is_test_mode = '$TestMode' WHERE id = '$idSite' LIMIT 1";
			$db2->query($sql);	
			
			echo $idSite . ' Sitio actualizado<br/>';
		}
	}else{
		echo 'Wrong Publisher ID';
	}
}

function migrateUser($idUser, $InsertUser = true){	
	global $db, $db2;
	
	
	$sql = "SELECT * FROM users WHERE id = '$idUser' LIMIT 1"; //users
	$query = $db->query($sql);
	$UserDate = $db->fetch_array($query);
	
	$User = $UserDate['user'];
		
	if($UserDate['deleted'] == 1){
		$Status = 4;
	}elseif($UserDate['AccM'] == 9999){
		$Status = 5;
		$User .= $idUser;
	}elseif($UserDate['verify_code'] == ''){
		$Status = 1;
	}elseif($UserDate['AccM'] == 15 && $UserDate['integrate'] == 1){
		$Status = 2;
	}elseif($UserDate['AccM'] == 15 && $UserDate['integrate'] == 0){
		$Status = 3;
	}elseif($UserDate['AccM'] == 15){
		$Status = 6;
	}else{
		$Status = 1;
	}
	
	
	
	$UserCanonical = strtolower($User);
	$Nick = $UserDate['nick'];
	$Pass = $UserDate['password'];
	$Email = $UserDate['email'];
	$EmailCanonical = strtolower($Email);
	$Name = mysqli_real_escape_string($db2->link, $UserDate['name']);
	$Last = mysqli_real_escape_string($db2->link, $UserDate['lastname']);
	$Phone = $UserDate['phone'];
	$Movil = $UserDate['movil'];
	$WA = $UserDate['whatsapp'];
	$Skype = $UserDate['sykpe'];
	$EF = $UserDate['ef'];
	$NIF = $UserDate['nifcif'];
	$Company = mysqli_real_escape_string($db2->link, $UserDate['company']);
	$Country = getNewCountryId($UserDate['country']);
	$Province = mysqli_real_escape_string($db2->link, $UserDate['province']);
	$City = mysqli_real_escape_string($db2->link, $UserDate['city']);
	$CP = $UserDate['cp'];
	$Address = mysqli_real_escape_string($db2->link, $UserDate['address']);
	$Currency = $UserDate['currency'];
	
	$Account = $UserDate['account'];
	$BankN = mysqli_real_escape_string($db2->link, $UserDate['bankname']);
	$BankA = mysqli_real_escape_string($db2->link, $UserDate['bankaddress']);
	$BackC = getNewCountryId($UserDate['bankcountry']);
	$BankCo = $UserDate['bankcurrency'];
	$IBAN = $UserDate['iban'];
	$Net = $UserDate['netterms'];
	$NetE = $UserDate['exceptions'];
	$Swift = $UserDate['swift'];
	$Amount = $UserDate['amount'];
	$LKQD = $UserDate['LKQD_id'];
	$LastL = $UserDate['lastlogin'];
	$ShowI = $UserDate['showi'];
	$idAccM  = $UserDate['AccM'];
	if(intval($idAccM) == 0){
		$idAccM = 1;
	}
	if(intval($idAccM) == 9999){
		$idAccM = 15;
	}
	$Campaing  = $UserDate['campaing'];
	$Keyword  = mysqli_real_escape_string($db2->link, $UserDate['keyword']);
	
	$createdAt = date('Y-m-d H:i:s', $UserDate['time']);
	
	if($UserDate['lastlogin'] > 0){
		$LLogin = date('Y-m-d H:i:s', $UserDate['lastlogin']);
	}else{
		$LLogin = '';
	}
	
	if($UserDate['paymenttype'] == 2){
		$PaymentT = 1;
	}else{
		$PaymentT = 2;
	}
	
	if($UserDate['lang'] == 'es'){
		$Lang = 'es';
	}
	else{
		$Lang = 'en';
	}
	
	if($BankCo == 0){$BankCo = 1;}
	
	$sql = "SELECT COUNT(*) FROM user WHERE id = '$idUser'";
	if($db2->getOne($sql) == 0){
		$RolesA = array('ROLE_PUBLISHER');
		$Roles = serialize($RolesA);
		
		$sql = "INSERT INTO user (
			id, 
			username, 
			username_canonical, 
			email, 
			email_canonical, 
			enabled, 
			password, 
			last_login,
			roles,
			name,
			last_name,
			status,
			created_at,
			locale
		) 
		VALUES (
			$idUser,
			'$User',
			'$UserCanonical',
			'$Email',
			'$EmailCanonical',
			1,
			'$Pass',
			'$LLogin',
			'$Roles',
			'$Name',
			'$Last', 
			$Status,
			'$createdAt',
			'$Lang'
		)";
		
		echo $idUser . ' Agregado';
		
		if($InsertUser){
			echo $sql . "\n";
			$db2->query($sql);
		}
	}else{
		$sql = "UPDATE user SET
			email = '$Email',
			email_canonical = '$EmailCanonical',
			password = '$Pass',
			last_name = '$Last',
			status = '$Status'
			WHERE id = '$idUser' LIMIT 1
		";
		if($InsertUser){
			$db2->query($sql);
		}
		
		echo $idUser . ' Existe, actualizado';
	}

	$sql = "SELECT id FROM publisher WHERE user_id = '$idUser'";
	$idPub = intval($db2->getOne($sql));
	if($idPub == 0){
		$sql = "INSERT INTO publisher (
			country_id, 
			currency_id, 
			bank_currency_id, 
			bank_country_id, 
			user_id, 
			account_manager_id, 
			phone,
			mobile,
			whatsapp,
			skype,
			fiscalid,
			fiscal_status,
			company, 
			province,
			city,
			zipcode,
			address,
			payment_type,
			account,
			bank_name,
			bank_address,
			iban,
			swift,
			amount,
			net_terms,
			lkqdid,
			is_direct_paid,
			contacts,
			send_connection_notice,
			campaign,
			adwords_keyword,
			allow_notifications,
			nickname
		) 
		VALUES (
			$Country,
			$Currency,
			$BankCo,
			$BackC,
			$idUser,
			$idAccM,
			'$Phone',
			'$Movil',
			'$WA',
			'$Skype', 
			'$NIF',
			'$EF',
			'$Company',
			'$Province',
			'$City',
			'$CP',
			'$Address',
			$PaymentT,
			'$Account',
			'$BankN',
			'$BankA',
			'$IBAN',
			'$Swift',
			'$Amount',
			'$Net',
			'$LKQD',
			1,
			'[]',
			0,
			'$Campaing',
			'$Keyword',
			0,
			'$Nick'
		)";
		echo $sql;			
		$db2->query($sql);
		
		echo ' - Publisher creado<br/>';
	}else{
		$sql = "UPDATE publisher SET
			country_id = '$Country', 
			bank_currency_id = '$BankCo', 
			bank_country_id = '$BackC', 
			account_manager_id = '$idAccM', 
			phone = '$Phone',
			mobile = '$Movil',
			whatsapp = '$WA',
			skype = '$Skype',
			fiscalid = '$NIF',
			fiscal_status = '$EF',
			company = '$Company', 
			province = '$Province',
			city = '$City',
			zipcode = '$CP',
			address = '$Address',
			payment_type = '$PaymentT',
			account = '$Account',
			bank_name = '$BankN',
			bank_address = '$BankA',
			iban = '$IBAN',
			swift = '$Swift',
			amount = '$Amount',
			net_terms = '$Net',
			lkqdid = '$LKQD',
			nickname = '$Nick'
		WHEFRE id = '$idPub' LIMIT 1";
		$db2->query($sql);
		
		echo ' - Publisher existe, actualizado<br/>';
	}
	
	
	$sql = "SELECT id FROM sites WHERE idUser = '$idUser'";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($S = $db->fetch_array($query)){
			//echo '<br/>' . $S['id'];
			migrateSite($S['id']);
		
		}
	}
	
	
}
	
	$sql = "SELECT id FROM users WHERE id = 26411"; //
	$query = $db->query($sql);
	while($U = $db->fetch_array($query)){
		migrateUser($U['id']);
	}
	
	
	//migrateUser(1058);
