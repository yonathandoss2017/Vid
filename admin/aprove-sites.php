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
		if(isset($_GET['ida'])){
			$idA = intval($_GET['ida']);
			if($idA > 0){
				$accmError = '';
				$accmErrorc = '';
				$accmErrors = '';
				$aprovError = '';
				$aprovErrorc = '';
				$motivoError = '';
				$motivoErrorc = '';
				
				$sql = "SELECT idUser FROM " . APROVE . " WHERE id = '$idA' LIMIT 1";
				$idUser = $db->getOne($sql);
				if($idUser > 0){
					$Aprov = 0;
					$Mot = 0;
					
					if(isset($_POST['save'])){
						$idASite = intval($_POST['idsite']);
						$Aprov = intval($_POST['aprov']);
						$Mot = intval($_POST['motivo']);
						
						if($Aprov == 1){
							$sql = "UPDATE " . APROVE . " SET Sites = CONCAT(Sites, '|$idASite=00') WHERE id = '$idA' LIMIT 1";
							$db->query($sql);
						}elseif($Aprov == 2){
							$sql = "UPDATE " . APROVE . " SET Sites = CONCAT(Sites, '|$idASite=0$Mot') WHERE id = '$idA' LIMIT 1";
							$db->query($sql);
						}
						
						$Aprov = 0;
						$Mot = 0;
					}
					
					$sql = "SELECT Sites FROM " . APROVE . " WHERE id = '$idA' LIMIT 1";
					$SitesString = $db->getOne($sql);
					
					$idSite = 0;
					$SiteToAprove = array();
					$sql = "SELECT * FROM " . SITES . " WHERE idUser = '$idUser'";
					$query = $db->query($sql);
					if($db->num_rows($query) > 0){
						while($Site = $db->fetch_array($query)){
							$idSite = $Site['id'];
							//echo var_dump(strpos($SitesString, "|$idSite=0"));
							if(strpos($SitesString, '|'.$Site['id'].'=0') === false){
								$SiteToAprove['id'] = $Site['id'];
								$SiteToAprove['siteurl'] = $Site['siteurl'];
								$SiteToAprove['views'] = $Site['views'];
								
								break;
							}
						}
					}
					
					//CHECK ADS.TXT
					if($idSite > 0){
						$AdsTxtState = checkAdsTxt($idSite, $SiteToAprove['siteurl']);
					}
				}
			}
		}
		if(!isset($SitesString)){
			header('Location: index.php?1');
			//echo 1;
			exit(0);
		}
		if(count($SiteToAprove) == 0){
			header('Location: aprove-finish.php?ida=' . $idA);
			exit(0);
		}
		//echo 1;
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
								<div class="titl">Aprobar Sitio: <a href="<?php echo domainToUrl($SiteToAprove['siteurl']); ?>" target="_blank" style="color:white;"><?php echo $SiteToAprove['siteurl']; ?></a></div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div id="formadd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Datos del Sitio</div>
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
															?><span style="display:inline-block; padding-top:13px; color:red;">No encontrado (<a href="aprove-sites.php?ida=<?php echo $idA; ?>">Volver a Chequear</a>)</span><?php
														}elseif($AdsTxtState == 1){
															?><span style="display:inline-block; padding-top:13px; color:red;">Incompleto (<a href="aprove-sites.php?ida=<?php echo $idA; ?>">Volver a Chequear</a>)</span><?php
														}else{
															?><span style="display:inline-block; padding-top:13px; color:green;">Completo</span><?php
														}
														?>
													</div>
												</div>
											</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd06">
												<!--<Aprobar>-->
												<div class="frm-group d-fx lbl-lf<?php echo $aprovErrorc; ?>">
													<label>Acci&oacute;n</label>
													<div class="d-flx1">
														<label<?php echo $aprovError; ?>>
															<select name="aprov" id="aprov" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Tipo">
																	<option value="1">Aprobar</option>
																	<option value="2">Denegar</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Aprobar>-->
											</div>
											<div class="clmd06" id="divmotivos" style="display:none;">
												<!--<Motivos>-->
												<div class="frm-group d-fx lbl-lf<?php echo $motivoErrorc; ?>">
													<label>Motivos</label>
													<div class="d-flx1">
														<label<?php echo $motivoError; ?>>
															<select name="motivo" id="motivo" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Tipo"><?php
																foreach($Motivos as $Key => $Motivo){
																	?><option value="<?php echo $Key; ?>"><?php echo $Motivo; ?></option><?php
																}
																
																?></optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Motivos>-->
											</div>
										</div>
										<div class="botnr-cn">
											<input type="hidden" value="<?php echo $idSite; ?>" name="idsite" />
											<input type="submit" class="fa-save" value="Guardar" name="save" />
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