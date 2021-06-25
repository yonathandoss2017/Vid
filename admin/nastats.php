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
	
	
	if(@$_SESSION['Admin'] == 1 && $_SESSION['Type'] == 3){
		$FoundUser = false;
		if(isset($_GET['iduser'])){
			$idUser = intval($_GET['iduser']);
			if($idUser > 0){
				$sql = "SELECT * FROM " . USERS . " WHERE id = '$idUser' AND AccM = 15 LIMIT 1";
				$query = $db->query($sql);
				if($db->num_rows($query) > 0){
					$UserData = $db->fetch_array($query);
					
					//CHECK ADS.TXT
					$AdsTxtState = checkAdsTxt($idSite, $SiteToAprove['siteurl']);
					$FoundUser = true;
				}
			}
		}
		if(!$FoundUser){
			header('Location: confirm.php');
			exit(0);
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
								<div class="titl">Estad&iacute;sticas: <?php echo $UserData['user'] . ' (' . $UserData['name'] . ' ' . $UserData['lastname'] . ')'; ?></div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd"><?php
									$sql = "SELECT * FROM " . SITES . " WHERE idUser = '$idUser'";
									$query = $db->query($sql);
									if($db->num_rows($query) > 0){
										while($Site = $db->fetch_array($query)){
											$idSite = $Site['id'];
										
											$jsonData = file_get_contents('http://pixel.vidoomy.com/viewsresults.php?siteid=' . $idSite);
											if($jsonData != 'null'){
												$CodeDetect = true;
												$siteData = json_decode($jsonData);
												if(count($siteData) > 0){
													$SiteStats = '<table><tr><td>D&iacute;a</td><td>Vistas</td></tr>';
													foreach($siteData as $date => $views){
														$SiteStats .= "<tr><td>$date</td><td>$views</td></tr>";
													}
													$SiteStats .= '</table>';
												}
												//echo $SiteStats;
											}else{
												$CodeDetect = false;
												//echo 'NO';
											}
											?><form action="" method="post" class="frm-adrsit">
												<div class="bx-hd dfl b-fx">
													<div class="titl">Datos del Sitio <?php echo $Site['siteurl']; ?></div>
												</div>
												<div class="clsd-fx">
													<div class="clmd06">
														<div class="frm-group d-fx lbl-lf">
															<label>C&oacute;digo detectado:</label>
															<div class="d-flx1"><?php
																if($CodeDetect){
																	?><span style="display:inline-block; padding-top:13px; color:green;">Si</span></div></div>
																	<div class="frm-group d-fx lbl-lf"><div class="d-flx1">
																	<?php
																	echo $SiteStats;
																}else{
																	?><span style="display:inline-block; padding-top:13px; color:red;">No</span><?php
																}
															?></div>
														</div>
													</div>
													<div class="clmd06">
														<div class="frm-group d-fx lbl-lf">
															<label>Ads.txt detectado:</label>
															<div class="d-flx1"><?php
																if($AdsTxtState == 2){
																	?><span style="display:inline-block; padding-top:13px; color:red;">No encontrado (<a href="nastats.php?iduser=<?php echo $idUser; ?>">Volver a Chequear</a>)</span><?php
																}elseif($AdsTxtState == 1){
																	?><span style="display:inline-block; padding-top:13px; color:red;">Incompleto (<a href="nastats.php?iduser=<?php echo $idUser; ?>">Volver a Chequear</a>)</span><?php
																}else{
																	?><span style="display:inline-block; padding-top:13px; color:green;">Completo</span><?php
																}
																?>
															</div>
														</div>
													</div>
												</div>
											</form><?php
										}
									}
									?>
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
		$("#aprov").change(function(){ 
			if($("#aprov").val() == 2){
				$("#divmotivos").show();
			}else{
				$("#divmotivos").hide();
			}
		});
		
		<?php if($Aprov != 0){ ?>
		$("#aprov").val(<?php echo $Aprov; ?>).trigger("change");
			<?php if($Aprov == 2){ ?>
			$(".divmotivos").show();
		<?php } } ?>
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