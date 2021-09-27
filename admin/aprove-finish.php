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
			
			//echo $sql;
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
		}
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
		
		if($InsertUser){
			$db2->query($sql);
		}
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
		//echo $sql;			
		$db2->query($sql);
	}
	
	$sql = "SELECT id FROM sites WHERE idUser = '$idUser'";
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($S = $db->fetch_array($query)){
			migrateSite($S['id']);
		}
	}
	
}
	
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	
	if(@$_SESSION['Admin'] == 1 && $_SESSION['Type'] == 3){
		if(isset($_GET['ida'])){
			$idA = intval($_GET['ida']);
			if($idA > 0){
				$accmError = '';
				$accmErrorc = '';
				$accmErrors = '';
				
				$sql = "SELECT idUser FROM " . APROVE . " WHERE id = '$idA' LIMIT 1";
				$idUser = $db->getOne($sql);
				if($idUser > 0){
					$sql = "SELECT Sites FROM " . APROVE . " WHERE id = '$idA' LIMIT 1";
					$SitesString = $db->getOne($sql);
					$sql = "SELECT * FROM " . APROVE . " WHERE id = '$idA' LIMIT 1";
					$query = $db->query($sql);
					$AprovData = $db->fetch_array($query);
					
					$AccM = $AprovData['AccM'];
					$SitesString = $AprovData['Sites'];
					$User = $AprovData['User'];
					
					if(strpos($SitesString, '=00') === false){
						$UserState = 0;
						$AccM = 9999;
					}else{
						$UserState = 1;
					}
					
					if(isset($_POST['save'])){
						$sql = "UPDATE " . USERS . " SET user = '$User', AccM = '$AccM' WHERE id = '$idUser' LIMIT 1";
						$db->query($sql);
						
						$arSites = explode('|', $SitesString);
						
						if(count($arSites > 0)){
							//print_r($arSites);
							foreach($arSites as $S){
								if($S != ''){
									$arS = explode('=', $S);
									$idSite = $arS[0];
									if(intval($arS[1]) == 0){
										$sql = "UPDATE " . SITES . " SET aproved = 0 WHERE id = '$idSite' LIMIT 1";
									}else{
										$mot = intval($arS[1]);
										$sql = "UPDATE " . SITES . " SET aproved = '$mot' WHERE id = '$idSite' LIMIT 1";
									}
									$db->query($sql);
								}
							}
						}
						
						$mail = new PHPMailer;
						
						notifyUserAccountState($idUser, $UserState);
						
						
						$sql = "DELETE FROM " . APROVE . " WHERE id = '$idA' LIMIT 1";
						$db->query($sql);
						
						migrateUser($idUser);
												
						header('Location: confirm.php');
						exit(0);
						
						
						
					}
					
					if(isset($_POST['save2'])){
						$sql = "DELETE FROM " . APROVE . " WHERE id = '$idA' LIMIT 1";
						$db->query($sql);
						header('Location: confirm.php');
						exit(0);
					}
				}else{
					header('Location: confirm.php');
					exit(0);
				}
			}else{
				header('Location: confirm.php');
				exit(0);
			}
		}
	
?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Vidoomy</title>
    <meta name="description" content="Vidoomy">
    <meta name="keywords" content="Vidoomy">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Cabin:400italic,600italic,700italic,400,600,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Lato:300,400,700,900' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/fa.css">
    <link rel="stylesheet" href="css/css.css">
    <link rel="icon" type="image/png" href="img/favicon.png">
</head>
<body>

	<!--<all>-->
	<div class="all">
	
		<?php include 'header.php'; ?>
		
		<!--<bdcn>-->
		<div class="bdcn">
			<div class="cnt">
				<!--<cls>-->
				<div class="cls c-fx">
                                        
					<!--<main>-->
					<main class="clmc12 c-flx1">
						<!--<Estadisticas Avanzadas>-->
						<div class="bx-cn bx-shnone">
							<div class="bx-hd dfl b-fx">
								<div class="titl">Confirmar</div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div class="bx-hd dfl b-fx">
										<div class="titl">Usuario <?php echo $User; ?> <?php if($UserState > 0){ ?>Aprobado<?php }else{ ?>Denegado<?php } ?></div>
									</div>
								</div>		
							</div>
							
							<!--<table>-->
							<div class="tbl-cn">
								<table id="tbl-estats">
									<thead>
										<tr>
											<th>Dominio</th>
											<th>Estado</th>
											<th>Motivo</th>
										</tr>
									</thead> 
									
									<tbody><?php
									
									$sql = "SELECT * FROM " . SITES . " WHERE idUser = '$idUser' ";
									$query = $db->query($sql);
									if($db->num_rows($query) > 0){
										while($Site = $db->fetch_array($query)){
											$idSite = $Site['id'];
											if(strpos($SitesString, $idSite . '=00') === false){
												$Estado = 'Denegado';
												
												if(strpos($SitesString, $idSite . '=02') !== false){
													$Motivo = $Motivos[2];
												}elseif(strpos($SitesString, $idSite . '=03') !== false){
													$Motivo = $Motivos[3];
												}else{
													$Motivo = $Motivos[4];
												}
											}else{
												$Estado = 'Aprobado';
												$Motivo = 'NA';
											}
											?><tr>
												<td data-title="Dominio"><?php echo $Site['siteurl']; ?></td>
												<td data-title="Estado"><?php echo $Estado; ?></td>
												<td data-title="Motivo"><?php echo $Motivo; ?></td>
											</tr><?php
										}
									}
									?>
									</tbody>
								</table>
							</div>
							<!--</table>-->
							
									<form action="" method="post" class="frm-adrsit">
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Confirmar y Notificar" name="save" />
											<input type="submit" class="fa-save" value="Cancelar" name="save2" />
										</div>
									</form>
									</div>
								</div>
								
							</div>
						</div>
						<!--</Estadisticas Avanzadas>-->
					</main>
					<!--<main>-->
					
				</div>
				<!--</cls>-->
			</div>
		</div>
		<!--</bdcn>-->
		
		<?php include 'footer.php'; ?>
			
	</div>
	<!--</all>-->
    
    <!-- Javascript -->
    <script src="js/lib/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-filestyle.js"></script>
</body>
</html>
</html><?php
	}else{
		header('Location: index.php');
		exit(0);
	}
?>