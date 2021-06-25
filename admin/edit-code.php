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
		
		if(isset($_GET['idcode'])){
			$codeId = intval($_GET['idcode']);
			if($codeId > 0){
				
				$cityError = '';
				$cityErrorc = '';
				$cityErrors = '';
				$typeError = '';
				$typeErrorc = '';
				$typeErrors = '';
				
				$sql = "SELECT * FROM " . CODES . " WHERE id = '$codeId' LIMIT 1";
				$query = $db->query($sql);
				$codeData = $db->fetch_array($query);
				$siteId = $codeData['idSite'];
				$type = $codeData['Type'];
				
				if($type == 3){
					$arLoc = explode('-',$codeData['Location']);
					$countLoc = $arLoc[0];
					
					$sql = "SELECT id FROM countries WHERE country_code = '$countLoc' LIMIT 1";
					$idC = $db->getOne($sql);
					
					$code = substr($codeData['Location'], 3);
					$sql = "SELECT Name FROM cities WHERE Code = '$code' AND idCountry = '$idC' LIMIT 1";
					$ccode = $db->getOne($sql);
				}else{
					$countLoc = $codeData['Location'];
					$ccode = '';
				}
				
				$sql = "SELECT * FROM " . SITES . " WHERE id = '$siteId' LIMIT 1";
				$query = $db->query($sql);
				$siteData = $db->fetch_array($query);
				$iduser = $siteData['idUser'];
				
				$arFilename = explode('/',$siteData['filename']);
				$filename = $arFilename[3];
				$filename2 = str_replace('.js','',$filename);
				
				if($type == 1){
					$filename = $filename2 . '-' . $codeData['Location'] . '.js';
				}else{
					$filename = $filename2 . '_' . $codeData['Location'] . '.js';
				}
				
				if(isset($_POST['save'])){
					
					if($_POST['save'] != ''){
						$txt = $_POST['appbundle_zonas']['codigo'];
						$myfile = fopen("../../Vidoomy/ads/$filename", "w") or die("Unable to open file!");
						fwrite($myfile, $txt);
						fclose($myfile);
							
						header('Location: edit-page.php?idpage=' . $siteId);
						exit(0);
						
					}
				}
				
				
				$myfile2 = fopen("../../Vidoomy/ads/$filename", "a+") or die("Unable to open file!");
				
				$code = '';
				while ($line = fgets($myfile2)) {
				  $code .= $line;
				}
				fclose($myfile2);
				
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
								<div class="titl">Editar Exepci&oacute;n para <?php echo $siteData['sitename']; ?></div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Código</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd06">
												<!--<Tipo>-->
												<div class="frm-group d-fx lbl-lf<?php echo $userErrorc; ?>">
													<label>Tipo</label>
													<div class="d-flx1">
														<label>
															<select name="type" id="type" disabled="disabled" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Tipo">
																	<option value="1">Continente</option>
																	<option value="2"<?php if($codeData['Type'] != 1){ echo ' selected="selected"';} ?>>País</option>
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
															<select name="ecity" id="ecity" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}' disabled="disabled">
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
												<div id="continent" class="frm-group d-fx lbl-lf"<?php if($codeData['Type'] != 1){ echo ' style="display:none;"';} ?>>
													<label>Continente</label>
													<div class="d-flx1">
														<label>
															<select name="cont" id="cont" disabled="disabled" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Continente"><?php
																	foreach($continents as $cv => $cn){
																		?><option value="<?php echo $cv; ?>"<?php if($codeData['Location'] == $cv){ echo ' selected="selected"';} ?>><?php echo $cn; ?></option><?php
																	}
																?></optgroup>
															</select>
														</label>
													</div>
												</div>
												<div id="country" class="frm-group d-fx lbl-lf"<?php if($codeData['Type'] == 1){ echo ' style="display:none;"';} ?>>
													<label>País</label>
													<div class="d-flx1">
														<label>
															<select name="count" id="count" disabled="disabled" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Pais"><?php
																	foreach($countries as $cv => $cn){
																		?><option value="<?php echo $cv; ?>"<?php if($countLoc == $cv){ echo ' selected="selected"';} ?>><?php echo $cn; ?></option><?php
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
																	<input type="text" name="city" id="city" value="<?php if($ccode != '') {echo $ccode;} ?>" disabled="disabled" />
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
</html>
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