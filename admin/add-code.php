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
	require('countries.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	if(@$_SESSION['Admin'] >= 1){
		
		if(isset($_GET['idpage'])){
			$siteId = intval($_GET['idpage']);
			if($siteId > 0){
				
				$ccode = '';
				$type = 1;
				
				$cityError = '';
				$cityErrorc = '';
				$cityErrors = '';
				$typeError = '';
				$typeErrorc = '';
				$typeErrors = '';
				
				$sql = "SELECT * FROM " . SITES . " WHERE id = '$siteId' LIMIT 1";
				$query = $db->query($sql);
				$siteData = $db->fetch_array($query);
				$iduser = $siteData['idUser'];
				
				$arFilename = explode('/',$siteData['filename']);
				$filename = $arFilename[3];
				$filename2 = str_replace('.js','',$filename);
				
				if(isset($_POST['save'])){
					$type = intval($_POST['type']);
					$cont = my_clean($_POST['cont']);
					$count = my_clean($_POST['count']);
					$ccode = my_clean($_POST['city']);
					
					if($type == 2){
						$ecity = intval($_POST['ecity']);
						if($ecity > 0){
							$type = 3;
						}
					}
					
					if($type == 1){
						$filename = $filename2 . '-' . $cont . '.js';
					}elseif($type == 2){
						$filename = $filename2 . '_' . $count . '.js';
					}
					
					if($_POST['save'] != ''){
						//echo 2;
						
						$sigue = true;
						
						if($type == 1){
							$Location = $cont;
						}elseif($type == 2){
							$Location = $count;
						}else{
							$newccode = '';

							$sql = "SELECT id FROM countries WHERE country_code = '$count' LIMIT 1";
							$idC = $db->getOne($sql);
							
							$sql = "SELECT Code FROM cities WHERE idCountry = '$idC' AND Name LIKE '$ccode' LIMIT 1";
							$newccode = $db->getOne($sql);
							if($newccode != ''){
								$filename = $filename2 . '_' . $count . '-' . $newccode . '.js';
								$Location = $count . '-' . $newccode;
							}else{
								$sigue = false;
								$cityError = ' data-error="Ciudad invalida."';
								$cityErrorc = ' frm-rrr';
								$cityErrors = ' style="margin-bottom:20px;"';
							}
						}
						
						if($sigue){
							
							$sql = "SELECT COUNT(*) FROM " . CODES . " WHERE idSite = '$siteId' AND Type = '$type' AND Location = '$Location' AND deleted != 1";
							
							if($db->getOne($sql) == 0){
								
								//echo 4;
								///print_r($_POST);
								$txt = $_POST['appbundle_zonas']['codigo'];
								$myfile = fopen("../../Vidoomy/ads/$filename", "w") or die("Unable to open file!");
								fwrite($myfile, $txt);
								fclose($myfile);
								
								$Time = time();
								$Date = date('Y-m-d');
							
								$sql = "INSERT INTO " . CODES . " (idSite, Type, Location, deleted, Time, Date)
								VALUES ('$siteId', '$type', '$Location', 0, '$Time', '$Date') ";
								$db->query($sql);
								
								header('Location: edit-page.php?idpage=' . $siteId);
								exit(0);
							}else{
								$typeError = ' data-error="Ya existe una exepción igual."';
								$typeErrorc = ' frm-rrr';
								$typeErrors = ' style="margin-bottom:20px;"';
							}
						}
					}
				}
				//echo "../../Vidoomy/ads/$filename";
				//$myfile2 = fopen("../../Vidoomy/ads/$filename", "a+") or die("Unable to open file!");
				
				$code = '';
				/*
				while ($line = fgets($myfile2)) {
				  $code .= $line;
				}
				fclose($myfile2);
				*/
				
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
    <link rel="stylesheet" href="css/autocomplete.css">
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
								<div class="titl">Crear Exepci&oacute;n para <?php echo $siteData['sitename']; ?></div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Crear Código</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd06">
												<!--<Tipo>-->
												<div class="frm-group d-fx lbl-lf<?php echo $userErrorc; ?>">
													<label>Tipo</label>
													<div class="d-flx1">
														<label<?php echo $typeError; ?>>
															<select name="type" id="type" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Tipo">
																	<option value="1">Continente</option>
																	<option value="2"<?php if($type != 1){ echo ' selected="selected"';}?>>País</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Tipo>-->
												<div id="elecity" class="frm-group d-fx lbl-lf<?php echo $typeErrorc; ?>"<?php if($type == 1) { echo 'style="display:none;"'; } ?>>
													<label>Elegir Ciudad</label>
													<div class="d-flx1">
														<label>
															<select name="ecity" id="ecity" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Continente">
																	<option value="0">No</option>
																	<option value="1"<?php if($type == 3) { echo ' selected="selected"'; } ?>>Si</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
											</div>
											<div class="clmd06">
												<!--<Ubicacion>-->
												<div id="continent" class="frm-group d-fx lbl-lf<?php echo $typeErrorc; ?>"<?php if($type != 1){ echo ' style="display:none"'; } ?>>
													<label>Continente</label>
													<div class="d-flx1">
														<label<?php echo $typeError; ?>>
															<select name="cont" id="cont" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Continente"><?php
																	foreach($continents as $cv => $cn){
																		?><option value="<?php echo $cv; ?>"><?php echo $cn; ?></option><?php
																	}
																?></optgroup>
															</select>
														</label>
													</div>
												</div>
												
												<div id="country" class="frm-group d-fx lbl-lf<?php echo $typeErrorc; ?>" <?php if($type == 1){ echo 'style="display:none;"'; } ?>>
													<label>País</label>
													<div class="d-flx1">
														<label<?php echo $typeError; ?>>
															<select name="count" id="count" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Pais"><?php
																$sql = "SELECT * FROM countries ORDER BY id ASC";
																$query = $db->query($sql);
																if($db->num_rows($query) > 0){
																	while($Co = $db->fetch_array($query)){
																		?><option value="<?php echo $Co['country_code']; ?>"<?php if($type != 1){ if($count == $Co['country_code']) { echo ' selected="selected"'; } } ?>><?php echo $Co['country_name']; ?></option><?php
																	}
																}
																?></optgroup>
															</select>
														</label>
													</div>
												</div>
												<div id="citydiv" class="frm-group d-fx lbl-lf<?php echo $cityErrorc; ?>" <?php if($type != 3) { echo 'style="display:none;"'; } ?>>
													<label>Ciudad</label>
													<div class="d-flx1">
														<label>
															<div class="d-flx1">
																<label class="lbl-icon ncn-lf"<?php echo $cityError; ?>>
																	<input type="text" name="city" id="city" value="<?php if($ccode != '') {echo $ccode;} ?>" />
																</label>
															</div>
														</label>
													</div>
												</div>
												<!--</Ubicacion>-->
											</div>
											<div class="clmd12">
												<div class="frm-nln">
													<label>Código</label>
													<div class="d-flx1" style="position:relative;">
													<input type="hidden" id="appbundle_zonas_codigo" name="appbundle_zonas[codigo]" value="<?php echo htmlspecialchars($code); ?>" />
														<div id="codigo"></div>
													</div>
													<div style="position:relative; clear:both;"><br/></div>
												</div>
											</div>
										</div>
										
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Guardar Cambios" name="save" />
										</div>
									</form>
								<div>		
								
									
									
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
    
    <style type="text/css">
	    #codigo { 
	        position: relative;
	        top: 0;
	        right: 0;
	        bottom: 0;
	        left: 0;
	        height:400px;
	    }
	</style>
    
    <!-- Javascript -->
    <script src="js/lib/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-filestyle.js"></script>
    <script src="js/jquery.autocomplete.js"></script>
    
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.8/ace.js" type="text/javascript" charset="utf-8"></script>
	<script>
		jQuery(document).ready(function($){
			<?php if($iduser > 0){ ?>
			$("#user").val(<?php echo $iduser; ?>).trigger("change");
			<?php } ?>
			$("#type").change(function(){
				if($("#type").val() == 1){
					$("#continent").show();
					$("#country").hide();
					$("#citydiv").hide();
					$("#elecity").hide();
				}else{
					$("#continent").hide();
					$("#country").show();
					$("#elecity").show();
				}
			});
			
			$("#ecity").change(function(){
				if($("#ecity").val() == 1){
					$("#citydiv").show();
				}else{
					$("#citydiv").hide();
				}
			});
			
			$( "#city" ).autocomplete({ 
			    serviceUrl: '/admin/autocompletecity.php',
			    params: {"c":$("#count").val()}
		    });
			
			$("#count").change(function(){
				$('#city').autocomplete('dispose');
				$( "#city" ).autocomplete({ 
				    serviceUrl: '/admin/autocompletecity.php',
				    params: {"c":$("#count").val()}
			    });
				//alert($("#count").val());
			});
			
			
		});
		
	    var editor = ace.edit("codigo");
	    editor.setTheme("ace/theme/monokai");
	    editor.getSession().setMode("ace/mode/javascript");
	
	    editor.getSession().on('change', function(e) {
	        $('#appbundle_zonas_codigo').val(editor.getValue())
	    });
	    editor.setValue($('#appbundle_zonas_codigo').val())
	
	</script>
</body>
</html><?php
			}else{
				header('Location: pages.php');
				exit(0);
			}
		}else{
			header('Location: pages.php');
			exit(0);
		}
	}else{
		header('Location: index.php');
		exit(0);
	}
?>