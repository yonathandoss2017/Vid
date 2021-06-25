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
	
	if(@$_SESSION['login'] >= 1){
		if(isset($_GET['siteid'])){
			$siteId = intval($_GET['siteid']);
			if($siteId > 0){
				$sql = "SELECT * FROM " . SITES . " WHERE id = '$siteId' AND idUser = '" . $_SESSION['login'] . "' LIMIT 1";
				$query = $db->query($sql);
				if($db->num_rows($query) > 0){
					$siteData = $db->fetch_array($query);
					$new = false;
					if(isset($_GET['new'])){
						if($_GET['new'] == 1){
							$new = true;
						}
					}
					
					$adstxt = '';
					$nl = "";
					$sql = "SELECT LKQD_id FROM " . USERS . " WHERE id = '$idPub' LIMIT 1";
					$idLkqd = $db->getOne($sql);
					$sql = "SELECT * FROM " . ADSTXT . " ORDER BY id ASC";
					$query = $db->query($sql);
					if($db->num_rows($query) > 0){
						while($Line = $db->fetch_array($query)){
							$LineTxt = str_replace('{LKQDID}', $idLkqd, $Line['LineTxt']);
							$adstxt .= $nl . $LineTxt;
							$nl = "\n";
						}
					}


					$adstxt = nl2br($adstxt);
					
					$buscar = array(chr(13).chr(10), "\r\n", "\n", "\r");
					$reemplazar = array("", "", "");
					$adstxt = str_ireplace($buscar,$reemplazar,$adstxt);
					
					$adstxt = str_replace('<br />', "\r", $adstxt);
	
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
								<div class="titl"><?php if($new) { ?>Site created successfully!<?php }else{ ?>Code for your site<?php } ?></div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<p>Below you have the code that you can integrate in your website.</p>
									<ul class="lst-codg">
										<li>
											<div class="b-fx">
												<figure>
                                                    <!--<h3>PRE-ROLL PC</h3>-->
                                                    <img src="img/cnt/pre-roll-pc.png" alt="">
                                                </figure>
												<div class="b-flx1">
													Code:<br/>
													<textarea cols="66" rows="6">&#60;script type="text/javascript" src="<?php echo str_replace('http://',"//",$siteData['filename']); ?>" &#62;&#60;/script&#62;</textarea>
													<button class="btn-blk fa-copy" onclick="copiarAlPortapapeles2('textatc2')">Click to copy the code</button>
													<input type="hidden" name="textatc2" id="textatc2" value='<script type="text/javascript" src="<?php echo str_replace('http://',"//",$siteData['filename']); ?>"></script>' />
													
													ads.txt:<br/>
													<textarea cols="66" rows="6" id="textatc"><?php echo $adstxt; ?></textarea>
													<button class="btn-blk fa-copy" onclick="copiarAlPortapapeles('textatc')">Click to copy the ads.txt</button>
													<input type="hidden" name="tocopy2" id="tocopy2" value='<?php echo $adstxt; ?>' />
												</div>
											</div>
										</li>
									</ul>
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
	<script type="text/javascript">
	
	function copiarAlPortapapeles(id_elemento) {
		var txt = document.getElementById(id_elemento).innerHTML;
		var textarea = document.createElement("textarea");
		//aux.setAttribute("value", document.getElementById(id_elemento).value);
		textarea.textContent = txt;
		document.body.appendChild(textarea);
		textarea.select();
		document.execCommand("copy");
		document.body.removeChild(textarea);	
	}
	function copiarAlPortapapeles2(id_elemento) {
		var txt = document.getElementById(id_elemento).value;
		var textarea = document.createElement("textarea");
		//aux.setAttribute("value", document.getElementById(id_elemento).value);
		textarea.textContent = txt;
		document.body.appendChild(textarea);
		textarea.select();
		document.execCommand("copy");
		document.body.removeChild(textarea);	
	}
	</script>
</body>
</html><?php
				}else{
					header('Location: sites.php');
					exit(0);
				}
			}else{
				header('Location: sites.php');
				exit(0);
			}
		}else{
			header('Location: sites.php');
			exit(0);
		}
	}else{
		header('Location: index.php');
		exit(0);
	}
?>