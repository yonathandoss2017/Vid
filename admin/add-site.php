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
	
	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	
	if(@$_SESSION['Admin'] == 1){
		

		$sql = "SELECT Name FROM " . ACC_MANAGERS . " WHERE id = '" . $_SESSION['idAdmin'] . "' LIMIT 1";
		$AccMName = $db->getOne($sql);
		if($AccMName == ''){
			header('Location: index.php');
			exit(0);
		}

		
		$sitename = '';
		$siteurl = '';
		$nameError = '';
		$nameErrorc = '';
		$nameErrors = '';
		$urlError = '';
		$urlErrorc = '';
		$urlErrors = '';
		$userError = '';
		$userErrorc = '';
		$errorAccept = '';
		$sel[1] = ' checked="checked"';
		$sel[2] = '';
		$sel[3] = '';
		$sel[4] = '';
		$sel[10] = '';
		
		if(isset($_GET['iduser'])){
			$iduser = intval($_GET['iduser']);
		}else{
			$iduser = 0;
		}
		
		if(isset($_POST['save'])){
			$sitename = my_clean($_POST['sitename']);
			$siteurl = my_clean($_POST['siteurl']);
			$category = intval($_POST['category']);
			$user = intval($_POST['user']);
			$sel[1] = '';
			$sel[$category] = ' checked="checked"';
			if($_POST['save'] != ''){
				if($_POST['sitename'] != ''){
					if($user > 0){
						if($_POST['siteurl'] != ''){
							if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$siteurl)) {
								$urlError = ' data-error="URL invalida."';
								$urlErrorc = ' frm-rrr';
								$urlErrors = ' style="margin-bottom:20px;"';
							}else{
								//call Google PageSpeed Insights API
								$googlePagespeedData = file_get_contents("https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url=$siteurl&screenshot=true");
								
								//decode json data
								$googlePagespeedData = json_decode($googlePagespeedData, true);
									
								//screenshot data
								$screenshot = $googlePagespeedData['screenshot']['data'];
								$screenshot = str_replace(array('_','-'),array('/','+'),$screenshot);
								
								$date = date('Y-m-d');
								$time = time();
								
								$Eric = 1;
									
								$sql = "INSERT INTO " . SITES . " (idUser, sitename, siteurl, category, image, filename, deleted, eric, test, time, date) VALUES ('$user', '$sitename', '$siteurl', '$category', '$screenshot', '', '0', '$Eric', '0', '$time', '$date')";
								$db->query($sql);
									
								$siteId = mysqli_insert_id($db->link);
								
								$filename = 'http://ads.vidoomy.com/' . convertirModRewrite($sitename) . '_' . $siteId . '.js';
								$filename2 = convertirModRewrite($sitename) . '_' . $siteId . '.js';
								$filename3 = convertirModRewrite($sitename) . '_' . $siteId;
								$sql = "UPDATE " . SITES . " SET filename = '$filename' WHERE id = '$siteId' LIMIT 1";
								$db->query($sql);
								
								
								//INSERT NUEVO PANEL
								$sql = "SELECT id FROM publisher WHERE user_id = '$user' LIMIT 1";
								$idPub = $db2->getOne($sql);
								
								if($idPub > 0){
									$sql = "INSERT INTO website (id, sitename, url, filename, status, is_test_mode, publisher_id) VALUES ($siteId, '$sitename', '$siteurl', '$filename', '5', '0', '$idPub')";
									$db2->query($sql);
								}
								
								//CHECK ADS.TXT
								$Url = urlToAdstxt($siteurl);
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
								
								if($NotFound !== true){
									$idUser = $user;
									$idSite = $siteId;
									
									//$sql = "SELECT LKQD_id FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
									//$idLkqd = $db->getOne($sql);
									//$idLkqd = 50306;
									
									$Coma = '';
									$N = 0;
									$Mlines = '';
									$Complete = true;
									
									$sql = "SELECT * FROM " . ADSTXT . " ORDER BY id ASC";
									$query = $db->query($sql);
									if($db->num_rows($query) > 0){
										while($Line = $db->fetch_array($query)){
											$N++;
											//$LineTxt = str_replace('{LKQDID}', $idLkqd, $Line['LineTxt']);
											//echo $LineTxt . ' - ';
											
											if($Line['id'] == 1){
												$LineTxt = 'vidoomy.com';
											}else{
												$LineTxt = $Line['LineTxt'];
											}
											
											if (stripos($AdsText, trim($LineTxt)) !== false) {
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
									}else{
										$sql = "UPDATE " . SITES . " SET adstxt = 1, mlines = '$Mlines' WHERE id = '$idSite' LIMIT 1";
									}
								}else{
									$sql = "UPDATE " . SITES . " SET adstxt = 2, mlines = '' WHERE id = '$idSite' LIMIT 1";
								}
								$db->query($sql);
								//FIN CHECK ADS.TXT
								
								
								$sql = "SELECT user FROM " . USERS . " WHERE id = '$user' LIMIT 1";
								$username = $db->getOne($sql);
								$titulo = $username . ' - ' . $sitename;
								
								$mail = new PHPMailer;
								
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
								$mail->addAddress('marcos.cuesta@vidoomy.com');
								
								$mail->AddCC('eric.raventos@vidoomy.com');
								$mail->AddCC('raquel.fernandez@vidoomy.com');
								$mail->AddCC('ivan.barrio@vidoomy.com ');
								$mail->AddCC('federico.izuel@vidoomy.com');
								
								$Subject = 'Nuevo sitio: ' . $titulo;
								
								$mail->Subject = $Subject;
								
								$message = '<html><body>';
								$message .= '<img src="http://login.vidoomy.com/img/vidoomy-logo.png" /><br /><br /><br />';

								$message .= "<p>Se ha añadido una nueva página:</p>";
								
								$message .= "<p>Dominio: $siteurl</p>";
								$message .= "<p>Publisher: $username</p>";
								$message .= "<p>Añadido por: $AccMName</p>";
								$message .= "</body></html>";
								
								$mail->msgHTML($message);
								$mail->send();
								
								header('Location: site-code.php?new=1&siteid=' . $siteId);
								exit(0);
							}
						
						}else{
							$urlError = ' data-error="Debe completar la URL del sitio web."';
							$urlErrorc = ' frm-rrr';
							$urlErrors = ' style="margin-bottom:20px;"';
						}
					}else{
						$userError = ' data-error="Debe seleccionar un Publisher."';
						$userErrorc = ' frm-rrr';
					}
				}else{
					$nameError = ' data-error="Debe completar el Nombre del sitio web."';
					$nameErrorc = ' frm-rrr';
					$nameErrors = ' style="margin-bottom:20px;"';
				}
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
								<div class="titl">Añadir Sitio Web</div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div id="formadd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Añadir Página</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd06">
												<!--<Nombre Web>-->
												<div class="frm-group d-fx lbl-lf<?php echo $nameErrorc; ?>">
													<label<?php echo $nameErrors; ?>>Nombre Web</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $nameError; ?>>
															<input type="text" name="sitename" value="<?php echo $sitename; ?>" />
															<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con el nombre del sitio web a añadir"></span>
														</label>
													</div>
												</div>
												<!--</Nombre Web>-->
												<!--<URL>-->
												<div class="frm-group d-fx lbl-lf<?php echo $urlErrorc; ?>">
													<label<?php echo $urlErrors; ?>>URL</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $urlError; ?>>
															<input type="text" name="siteurl" value="<?php echo $siteurl; ?>" />
															<span class="fa-info tt-lt" data-toggle="tooltip" title="Rellena este campo con la URL de tu sitio web a añadir"></span>
														</label>
													</div>
												</div>
												<!--</URL>-->
											</div>
											<div class="clmd06">
												
												<!--<Publisher>-->
												<div class="frm-group d-fx lbl-lf<?php echo $userErrorc; ?>">
													<label>Publisher</label>
													<div class="d-flx1">
														<label<?php echo $userError; ?>>
															<select name="user" id="user" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Publisher"><?php
																	if($_SESSION['Type'] == 3){
																		$sql = "SELECT * FROM " . USERS . " ORDER BY user ASC";
																	}else{
																		$idAccM = $_SESSION['idAdmin'];
																		$sql = "SELECT * FROM " . USERS . " WHERE AccM = '$idAccM' ORDER BY user ASC";
																	}
																	$query = $db->query($sql);
																	if($db->num_rows($query) > 0){
																		while($User = $db->fetch_array($query)){
																			?><option value="<?php echo $User['id']; ?>"><?php echo $User['user']; ?></option><?php
																		}
																	}
																?></optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Publisher>-->
												<div class="frm-nln">Tipo de contenido 
													<label class="option"><input name="category" type="radio" value="1"<?php echo $sel[1]; ?>> Periódico </label>
													<label class="option"><input name="category" type="radio" value="2"<?php echo $sel[2]; ?>> TV online </label>
													<label class="option"><input name="category" type="radio" value="3"<?php echo $sel[3]; ?>> Viral </label>
													<label class="option"><input name="category" type="radio" value="4"<?php echo $sel[4]; ?>> Juegos </label>
													<label class="option"><input name="category" type="radio" value="10"<?php echo $sel[10]; ?>> Otros</label>
												</div>
											</div>
										</div>
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Guardar Página" name="save" /> 	
										</div>
									</form>
									</div>
									<div id="imgloading" style="width:256px; margin:auto; display:none;">
										<img src="img/loading.gif" />
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
    <script>
	    
	jQuery(document).ready(function($){
		<?php if($iduser > 0){ ?>
		$("#user").val(<?php echo $iduser; ?>).trigger("change");
		<?php } ?>
	});
	
	jQuery('#formadd').submit(function($){
		jQuery("#formadd").hide();
		jQuery("#imgloading").show();
		//alert('A');
	});
	
	</script>
</body>
</html>
</html><?php
	}else{
		header('Location: index.php');
		exit(0);
	}
?>