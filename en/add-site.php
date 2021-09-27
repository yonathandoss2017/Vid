<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('common.lib.php');

	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	$db2 = new SQL($pubProd['host'], $pubProd['db'], $pubProd['user'], $pubProd['pass']);
	
	require '/var/www/html/site/include/PHPMailer/PHPMailerAutoload.php';
	
	if(@$_SESSION['login'] >= 1){
		$sitename = '';
		$siteurl = '';
		$nameError = '';
		$nameErrorc = '';
		$nameErrors = '';
		$urlError = '';
		$urlErrorc = '';
		$urlErrors = '';
		$errorAccept = '';
		$sel[1] = ' checked="checked"';
		$sel[2] = '';
		$sel[3] = '';
		$sel[4] = '';
		$sel[10] = '';
		
		if(isset($_POST['save'])){
			$sitename = my_clean($_POST['sitename']);
			$siteurl = my_clean($_POST['siteurl']);
			$category = intval($_POST['category']);
			$sel[1] = '';
			$sel[$category] = ' checked="checked"';
			if($_POST['save'] != ''){
				if($_POST['sitename'] != ''){
					if($_POST['siteurl'] != ''){
						
						
						if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$siteurl)) {
							$urlError = ' data-error="Invalid URL."';
							$urlErrorc = ' frm-rrr';
							$urlErrors = ' style="margin-bottom:20px;"';
						}else{
							if(isset($_POST['accept'])){
								//call Google PageSpeed Insights API
								$googlePagespeedData = file_get_contents("https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url=$siteurl&screenshot=true");
								
								//decode json data
								$googlePagespeedData = json_decode($googlePagespeedData, true);
								//print_r($googlePagespeedData);
								
								//screenshot data
								$screenshot = $googlePagespeedData['screenshot']['data'];
								$screenshot = str_replace(array('_','-'),array('/','+'),$screenshot);
								
								$date = date('Y-m-d');
								$time = time();
								
								$Eric = 1;
								
								$sql = "INSERT INTO " . SITES . " (idUser, sitename, siteurl, category, image, filename, deleted, eric, test, time, date) VALUES ('" . $_SESSION['login'] . "', '$sitename', '$siteurl', '$category', '$screenshot', '', '0', '$Eric', '0', '$time', '$date')";
								$db->query($sql);
								
								$siteId = mysqli_insert_id($db->link);
								
								$filename = 'http://ads.vidoomy.com/' . convertirModRewrite($sitename) . '_' . $siteId . '.js';
								$filename2 = convertirModRewrite($sitename) . '_' . $siteId . '.js';
								$filename3 = convertirModRewrite($sitename) . '_' . $siteId;
								
								$sql = "UPDATE " . SITES . " SET filename = '$filename' WHERE id = '$siteId' LIMIT 1";
								$db->query($sql);
								
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
									$idUser = $_SESSION['login'];
									$idSite = $siteId;
									
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
								
								$sql = "SELECT user FROM " . USERS . " WHERE id = '" . $_SESSION['login'] . "' LIMIT 1";
								$username = $db->getOne($sql);
								$titulo = $username . ' - ' . $sitename;
								$sql = "SELECT AccM FROM " . USERS . " WHERE id = '" . $_SESSION['login'] . "' LIMIT 1";
								$idAccM = $db->getOne($sql);
								if(intval($idAccM) > 0){
									$sql = "SELECT Name FROM " . ACC_MANAGERS . " WHERE id = '$idAccM' LIMIT 1";
									$AccM = $db->getOne($sql);
									$sql = "SELECT Email FROM " . ACC_MANAGERS . " WHERE id = '$idAccM' LIMIT 1";
									$AccMEmail = $db->getOne($sql);
								}else{
									$AccM = 'No asignado';
								}
								
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
								
								$mail->AddCC('raquel.fernandez@vidoomy.com');
								$mail->AddCC('eric.raventos@vidoomy.com');
								$mail->AddCC('ivan.barrio@vidoomy.com');
								$mail->AddCC('federico.izuel@vidoomy.com');
								
								if($idAccM > 0){
									if($idAccM != 6 && $idAccM != 10){
										$mail->AddCC($AccMEmail);
									}
								}
								
								$Subject = 'Nuevo sitio: ' . $titulo;
								
								$mail->Subject = $Subject;
								
								$message = '<html><body>';
								$message .= '<img src="http://login.vidoomy.com/img/vidoomy-logo.png" /><br /><br /><br />';

								$message .= "<p>Se ha añadido una nueva página:</p>";
								
								$message .= "<p>Dominio: $siteurl</p>";
								$message .= "<p>Publisher: $username</p>";
								$message .= "<p>Añadido por: Publisher</p>";
								$message .= "<p>Publisher Manager: $AccM</p>";
								$message .= "</body></html>";
								
								$mail->msgHTML($message);
								$mail->send();
								
								//INSERT NUEVO PANEL
								$sql = "SELECT id FROM publisher WHERE user_id = '" . $_SESSION['login'] . "' LIMIT 1";
								$idPub = $db2->getOne($sql);
								
								if($idPub > 0){
									$sql = "INSERT INTO website (id, sitename, url, filename, status, is_test_mode, publisher_id) VALUES ($idSite, '$sitename', '$siteurl', '$filename', '5', '0', '$idPub')";
									$db2->query($sql);
								}
									
								header('Location: site-code.php?new=1&siteid=' . $siteId);
								exit(0);
							}else{
								$errorAccept = ' style="color:red;"';
							}
						}
					}else{
						$urlError = ' data-error="You must complete the website URL."';
						$urlErrorc = ' frm-rrr';
						$urlErrors = ' style="margin-bottom:20px;"';
					}
				}else{
					$nameError = ' data-error="You must complete the website Name."';
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
								<div class="titl">Add Website</div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div id="formadd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Add Site</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd06">
												<!--<Nombre Web>-->
												<div class="frm-group d-fx lbl-lf<?php echo $nameErrorc; ?>">
													<label<?php echo $nameErrors; ?>>Website</label>
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
												<div class="frm-nln">Content type 
													<label class="option"><input name="category" type="radio" value="1"<?php echo $sel[1]; ?>> Newspaper </label>
													<label class="option"><input name="category" type="radio" value="2"<?php echo $sel[2]; ?>> Online TV </label>
													<label class="option"><input name="category" type="radio" value="3"<?php echo $sel[3]; ?>> Viral </label>
													<label class="option"><input name="category" type="radio" value="4"<?php echo $sel[4]; ?>> Games </label>
													<label class="option"><input name="category" type="radio" value="10"<?php echo $sel[10]; ?>> Others</label>
												</div>
												<div class="tpcnw">
													<p>Please select the correct type of content of the site because the ads depend on the type of content of the site.</p>
												</div>
											</div>
										</div>
										<div class="infimp">
											<label<?php echo $errorAccept; ?>><input name="accept" type="checkbox" value="1"> <strong>Important:</strong> You are adding a new site on the Vidoomy system, you declare with this action that you are the owner of the site, if you aren't and you add it, you can be involved in legal problems related to identity theft or plagiarism. <!--Una vez dada de alta pasarás a la sección de zonas para crear las publicidades.--></label>
										</div>
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Save" name="save" /> <button class="fa-times-circle cancelar" onclick="location.href='sites.php'">Cancel</button>
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