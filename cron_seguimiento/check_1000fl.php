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
	
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
		
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
	
	mysqli_set_charset($db2->link,'utf8');

		
function getTodayMore1000FL($LimitForNew = 1000){
	global $db, $db2; 
	
	$Today = date('Y-m-d');
	//$Today = date('Y-m-27');
	$ResT = date('Ym');
	
	$sql = "SELECT SUM(formatLoads) AS FL, Domain AS idDomain, idUser AS idUser, idSite AS idSite

		FROM `reports_resume$ResT`
		
		WHERE Date = '$Today'
		
		GROUP BY idDomain, idUser, idSite
		
		ORDER BY FL DESC";
		
	$query = $db->query($sql);
	if($db->num_rows($query) > 0){
		while($Dom = $db->fetch_array($query)){
			if($Dom['FL'] >= $LimitForNew){
				//$dateLM->modify('-1 month');
				$idDomain = $Dom['idDomain'];
				$idUser = $Dom['idUser'];
				$idSite = $Dom['idSite'];
				
				//echo $idDomain . "\n";
				
				if($idDomain != 386){
				
					$NotNew = false;
					
					$sql = "SELECT SUM(formatLoads) AS FL FROM reports_resume$ResT WHERE Domain = $idDomain AND idUser = $idUser AND Date < '$Today' GROUP BY Date";
					$query2 = $db->query($sql);
					if($db->num_rows($query2) >= 0){
						while($Prev = $db->fetch_array($query2)){
							///echo $idDomain . ' : ' . $Prev['FL'] . "\n";
							if($Prev['FL'] > $LimitForNew){
								$NotNew = true;
								break;
							}
						}
					}
					
					if($NotNew === false){
						//echo "Posible Nuevo: $sql \n";
						
						$sql = "SELECT Name FROM reports_domain_names WHERE id = '$idDomain'";
						$Domain = $db->getOne($sql);
						
						//echo $Domain . "\n";
	
						$arD = explode('.', $Domain);
						if(count($arD) > 2){
							$DomLike = $arD[1] . '.' . $arD[2];
						}else{
							$DomLike = $Domain;
						}
						
						$sql = "SELECT COUNT(*) FROM report_domain_black_list WHERE name LIKE '$DomLike'";
						if($db2->getOne($sql) == 0){
						
							$DateForTable = new DateTime($Today);
							
							for($I = 0; $I <= 12; $I++){
								
								$DateForTable->modify('-1 month');
								$ResT2 = $DateForTable->format('Ym');
								
								$sql = "SELECT SUM(formatLoads) AS FL FROM reports_resume$ResT2 WHERE Domain = $idDomain AND idUser = $idUser AND Date < '$Today' GROUP BY Date";
								$query2 = $db->query($sql);
								if($db->num_rows($query2) >= 0){
									while($Prev = $db->fetch_array($query2)){
										//echo $idDomain . ' : ' . $Prev['FL'] . "\n";
										if($Prev['FL'] > $LimitForNew){
											$NotNew = true;
											break;
										}
									}
								}
								if($NotNew){
									//echo $ResT2;
									break;
								}
							}
							
							if($NotNew === false){
								echo "\n NUEVO DOMINIO \n\n";
								
								$sql = "SELECT COUNT(*) FROM sent_activation_domain WHERE idDomain = $idDomain AND idUser = $idUser";
								if($db->getOne($sql) == 0){
								
									$sql = "SELECT test FROM sites WHERE id = '$idSite' LIMIT 1";
									$isTestMode = $db->getOne($sql);
									
									//pub manager, team leader, marcos y eric
									
									$sql = "SELECT account_manager_id FROM publisher WHERE user_id = $idUser LIMIT 1";
									$PubManagereId = $db2->getOne($sql);
									
									if($PubManagereId != 2){
										$sql = "INSERT INTO sent_activation_domain (idDomain, idUser, Date) VALUES ($idDomain, $idUser, '$Today')";
										$db->query($sql);
										
										$sql = "SELECT username FROM user WHERE id = $idUser LIMIT 1";
										$Publisher = $db2->getOne($sql);
										
										$sql = "SELECT email FROM user WHERE id = $PubManagereId LIMIT 1";
										$EmailPubManager = $db2->getOne($sql);
										
										$sql = "SELECT CONCAT(name, ' ', last_name) FROM user WHERE id = $PubManagereId LIMIT 1";
										$NamePubManager = $db2->getOne($sql);
										
										$sql = "SELECT manager_id FROM user WHERE id = $PubManagereId LIMIT 1";
										$SubHeadId = $db2->getOne($sql);
										
										
										echo "Pub Manager ID: $PubManagereId \n";
										echo "Pub Manager Name: $NamePubManager \n";
										echo "Pub Manager Mail: $EmailPubManager \n";
										
										
										$OriginalTpl = file_get_contents('/var/www/html/login/emailstpl/new_domain.html');
										$EmailText = str_replace('#PubManager#', $NamePubManager, $OriginalTpl);
										$EmailText = str_replace('#Domain#', $Domain, $EmailText);
										$EmailText = str_replace('#Publisher#', $Publisher, $EmailText);
										
										if($isTestMode){
											$EmailText = str_replace('#Test#', ', y tiene aún el test activo', $EmailText);
											$EmailTitle = "El dominio $Domain activó, y OJO tiene el test activo";
											
											
										}else{
											$EmailText = str_replace('#Test#', '', $EmailText);
											$EmailTitle = "El dominio $Domain activó";
										}
										
										$mail = new PHPMailer;
										$mail->isSMTP();
										$mail->SMTPDebug = false;
										$mail->Debugoutput = 'html';
										
										$mail->Host = 'smtp.gmail.com';
										$mail->Port = 465;
										$mail->SMTPSecure = 'ssl';
										$mail->SMTPAuth = true;
										//$mail->Username = "notify@vidoomy.net";
										//$mail->Password = "NosdFiY-98";
										$mail->Username = "notifysystem@vidoomy.net";
										$mail->Password = "NoTyFUCK05-1";
										$mail->CharSet = 'UTF-8';
										$mail->setFrom('notifysystem@vidoomy.net', 'Vidoomy');
										$mail->addReplyTo('notifysystem@vidoomy.net', 'Vidoomy');
										
										//$EmailPubManager = 'federicoizuel@gmail.com';
										$mail->addAddress($EmailPubManager, $NamePubManager);
										$mail->AddBCC('federico.izuel@vidoomy.com');
										$mail->AddBCC('marcos.cuesta@vidoomy.com');
										$mail->AddBCC('eric.raventos@vidoomy.com');
										$mail->AddBCC('angel.burgos@vidoomy.com');
										$mail->AddBCC('raquel.fernandez@vidoomy.com');
										$mail->AddBCC('gadiel.reyesdelrosario@vidoomy.com');
										
										
										if(intval($SubHeadId) > 0){
											if($PubManagereId != $SubHeadId){
												$sql = "SELECT email FROM user WHERE id = $SubHeadId LIMIT 1";
												$EmailSubHead = $db2->getOne($sql);
												/*
												$sql = "SELECT CONCAT(name, ' ', last_name) FROM user WHERE id = $SubHeadId LIMIT 1";
												$NameSubHead = $db2->getOne($sql);
												*/
												//echo "Head Name: $NameHead \n";
												//echo "Head Email: $EmailHead \n";
												$mail->AddCC($EmailSubHead);
												
												$sql = "SELECT manager_id FROM user WHERE id = $SubHeadId LIMIT 1";
												$HeadId = $db2->getOne($sql);
												
												if(intval($HeadId) > 0){
													if($PubManagereId != $HeadId && $SubHeadId != $HeadId){
														$sql = "SELECT email FROM user WHERE id = $HeadId LIMIT 1";
														$EmailHead = $db2->getOne($sql);
														
														$mail->AddCC($EmailHead);
													}
												}
											}
										}
										
										
										$mail->Subject = $EmailTitle;
										$mail->msgHTML($EmailText);
										$mail->send();
			
									}
								}
							}
						}
					}
				}
			}
		}
	}
}

	getTodayMore1000FL(1000);