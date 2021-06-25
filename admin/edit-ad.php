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
	//require('countries.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	require('libs/display.lib.php');
	
	$dbuser2 = "root";
	$dbpass2 = "ViDo0-PROD_2020";
	$dbhost2 = "aa12gqfb9qs8z09.cme5dsqa4tew.us-east-2.rds.amazonaws.com:3306";
	$dbname2 = "vidoomy";
	$db2 = new SQL($dbhost2, $dbname2, $dbuser2, $dbpass2);
	
	if(@$_SESSION['Admin'] >= 1){
		
		if(isset($_GET['idpage'])){
			$siteId = intval($_GET['idpage']);
			if($siteId > 0){
				if(isset($_GET['idad'])){
					$idAd = intval($_GET['idad']);
					if($idAd > 0){
						$sql = "SELECT * FROM " . ADS . " WHERE id = '$idAd' LIMIT 1";
						$query = $db->query($sql);
						$AdData = $db->fetch_array($query);
						
						$Type = $AdData['Type'];
						$idLkqd = $AdData['idLKQD'];
						$divID = $AdData['divID'];
						$Width = $AdData['Width'];
						$Height = $AdData['Height'];
						$Close = $AdData['Close'];
						$DFP = $AdData['DFP'];
						$Override = $AdData['Override'];
						$Spos = $AdData['SPosition'];
						$AA = $AdData['HeightA'];
						$code = $AdData['CCode'];
				
						$typeError = '';
						$typeErrors = '';
						$typeErrorc = '';
						$idLkqdError = '';
						$idLkqdErrors = '';
						$idLkqdErrorc = '';
						$divIDError = '';
						$divIDErrors = '';
						$divIDErrorc = '';
						$WidthError = '';
						$WidthErrors = '';
						$WidthErrorc = '';
						$HeightError = '';
						$HeightErrors = '';
						$HeightErrorc = '';
						$dfpError = '';
						$dfpErrorc = '';
						$dfpErrors = '';
						$overrideError = '';
						$overrideErrorc = '';
						$closeError = '';
						$closeErrorc = '';
						$sposError = '';
						$sposErrorc = '';				
						$aaError = '';
						$aaErrors = '';
						$aaErrorc = '';
						
						if($Type == 10){
							$Platform = 1;
						}elseif($Type == 11){
							$Platform = 2;
						}
						
						$sql = "SELECT * FROM " . SITES . " WHERE id = '$siteId' LIMIT 1";
						$query = $db->query($sql);
						$siteData = $db->fetch_array($query);
						$iduser = $siteData['idUser'];
						$ExcludeDisplay = $siteData['ed'];
								
						if(isset($_POST['save'])){
							//print_r($_POST);
							$Type = intval($_POST['type']);
							
							if($Type == 100){
								$CCode = $_POST['appbundle_zonas']['codigo'];
							}elseif($Type >= 10){
								$CCode = '';
							}else{
								$CCode = '';
								
								$idLkqd = my_clean($_POST['idLkqd']);
								$divID = my_clean($_POST['divID']);
								$Width = my_clean($_POST['Width']);
								$Height = my_clean($_POST['Height']);
								$DFP = intval($_POST['dfp']);
								$Override = intval($_POST['override']);
								$Close = intval($_POST['close']);
								$Spos = intval($_POST['spos']);
								$AA = my_clean($_POST['aa']);
							}
												
							if($_POST['save'] != ''){
								$sigue = true;
								
								if($Type >= 10 && $Type < 20){
									$NewAdUnits = '';
									if(isset($_POST['adunit'])){
										if(is_array($_POST['adunit'])){
											foreach($_POST['adunit'] as $idAdUnit){
												$NewAdUnits .= '{' . $idAdUnit . ':' . $_POST['position-'.$idAdUnit] . '}';
											}
										}
									}
									
									if($NewAdUnits == ''){
										$sigue = false;
										$ErrorNoAdUnits = 'No se puede eliminar todos los Ad Units.';
									}
								}else{
								
									if($idLkqd == '' && $Type != 100){
										$idLkqdError = ' data-error="Debe ingresar el ID de LKQD."';
										$idLkqdErrorc = ' frm-rrr';
										$idLkqdErrors = ' style="margin-bottom:20px;"';
										
										$sigue = false;
									}
									
									if($divID == '' && ($Type == 1 || $Type == 2)){
										$divIDError = ' data-error="Debe ingresar el ID del DIV."';
										$divIDErrorc = ' frm-rrr';
										$divIDErrors = ' style="margin-bottom:20px;"';
										
										$sigue = false;
									}
									
									if($Width == 0 && $Type != 100){
										$WidthError = ' data-error="El ancho debe ser mayor a 0."';
										$WidthErrorc = ' frm-rrr';
										$WidthErrors = ' style="margin-bottom:20px;"';
										
										$sigue = false;
									}
									
									if($Height == 0 && $Type != 100){
										$HeightError = ' data-error="Debe alto debe ser mayor a 0."';
										$HeightErrorc = ' frm-rrr';
										$HeightErrors = ' style="margin-bottom:20px;"';
										
										$sigue = false;
									}
									
									if($DFP == 0 && $Type != 100){
										$dfpError = ' data-error="Debe completar el campo DFP."';
										$dfpErrorc = ' frm-rrr';
										$dfpErrors = ' style="margin-bottom:20px;"';
										
										$sigue = false;
									}
								}
								
								if($sigue){
									if($Type >= 10 && $Type < 20){
										$CCode = $NewAdUnits;
										$Width = '';
										$Height = '';
										$Close = 0;
										$Override = 0;
										$Spos = 0;
									}
									
									if($Type == 3){
										if(isset($_POST['displayauto'])){
											$sql = "UPDATE " . SITES . " SET ed = 1 WHERE id = '$siteId' LIMIT 1";
											$db->query($sql);
											$sql = "DELETE FROM " . ADS . " WHERE idLKQD = 'AUTO' AND idSite = $idSite";
											$db->query($sql);
										}else{
											$sql = "UPDATE " . SITES . " SET ed = 0 WHERE id = '$siteId' LIMIT 1";
											$db->query($sql);
										}
										
									}
									
									$CCode = mysqli_real_escape_string($db->link, $CCode);
									$sql = "UPDATE " . ADS . " SET idLKQD = '$idLkqd', divID = '$divID', Width = '$Width', Height = '$Height', Close = '$Close', DFP = '$DFP', Override = '$Override', HeightA = '$AA', SPosition = '$Spos', CCode = \"$CCode\" WHERE id = '$idAd' LIMIT 1";
									$db->query($sql);
									
									$sql = "UPDATE ad SET lkqdid = '$idLkqd', divid = '$divID', width = '$Width', height = '$Height', close = '$Close', dfp = '$DFP', override = '$Override', height_a = '$AA', sposition = '$Spos', ccode = \"$CCode\" 
									WHERE id = '$idAd' LIMIT 1";
									$db2->query($sql);
									
									newGenerateJS($siteId);
									
									header('Location: edit-page.php?idpage='.$siteId);
									exit(0);
									
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
								<div class="titl">Editar Anuncio para <?php echo $siteData['sitename']; ?></div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Editar Anuncio</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd12">
												<!--<Tipo>-->
												<div class="frm-group d-fx lbl-lf<?php echo $typeErrorc; ?>">
													<label>Tipo</label>
													<div class="d-flx1">
														<label<?php echo $typeError; ?>>
															<select name="type" id="type" disabled="disabled" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Tipo"><?php
																	foreach($AdType2 as $K => $V){
																		?><option value="<?php echo $K; ?>" <?php if ($Type == $K) { echo  ' selected="selected"'; } ?>><?php echo $V; ?></option><?php
																	}
																?></optgroup>
															</select>
															<input type="hidden" name="type" value="<?php echo $Type; ?>" />
														</label>
													</div>
												</div>
												<!--</Tipo>-->
											</div>
											
											
											<?php if($Type >= 10 && $Type < 20){ ?>
											
											<div class="clmd12 displayads">
												<div class="bx-pd" id="displayadload">
													<?php displayNewDisplayAd($Platform, $idAd); ?>
												</div>
												<br /><br /><br />
											</div>
											
											<?php }else{ ?>
											
											<div class="clmd06">
												<div class="hide-cc">
												<!--<ID LKQD>-->
												<div class="frm-group d-fx lbl-lf<?php echo $idLkqdErrorc; ?>">
													<label<?php echo $idLkqdErrors; ?>>ID LKQD</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $idLkqdError; ?>>
															<input type="text" name="idLkqd" value="<?php echo $idLkqd; ?>" />
														</label>
													</div>
												</div>
												<!--</ID LKQD>-->
												<div class="no-slider-type">
												<!--<Div ID>-->
												<div class="frm-group d-fx lbl-lf<?php echo $divIDErrorc; ?>">
													<label<?php echo $divIDErrors; ?>>Div ID</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $divIDError; ?>>
															<input type="text" name="divID" value="<?php echo $divID; ?>" />
														</label>
													</div>
												</div>
												<!--</Div ID>-->
												</div> 
												<!--<DFP>-->
												<div class="frm-group d-fx lbl-lf<?php echo $dfpErrorc; ?>">
													<label>DFP</label>
													<div class="d-flx1">
														<label<?php echo $dfpError; ?>>
															<select name="dfp" id="dfp" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona">
																	<option value="0"></option>
																	<option value="1" <?php if ($DFP == 1) { echo  ' selected="selected"'; } ?>>true</option>
																	<option value="2" <?php if ($DFP == 2) { echo  ' selected="selected"'; } ?>>false</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</DFP>-->
												<div class="slider-type">
												<!--<Close>-->
												<div class="frm-group d-fx lbl-lf<?php echo $closeErrorc; ?>">
													<label>Close</label>
													<div class="d-flx1">
														<label<?php echo $closeError; ?>>
															<select name="close" id="close" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona">
																	<option value="1">true</option>
																	<option value="2" <?php if ($Close == 2 || $Close == 0) { echo  ' selected="selected"'; } ?>>false</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Close>-->
												<!--<Slider Position>-->
												<div class="frm-group d-fx lbl-lf<?php echo $sposErrorc; ?>">
													<label>Slider Position</label>
													<div class="d-flx1">
														<label<?php echo $sposError; ?>>
															<select name="spos" id="spos" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona">
																	<option value="1">right</option>
																	<option value="2" <?php if ($Spos != 1) { echo  ' selected="selected"'; } ?>>left</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Slider Position>-->
												</div>
												</div>
											</div>
											<div class="clmd06">
												<div class="hide-cc">
												<!--<Width>-->
												<div class="frm-group d-fx lbl-lf<?php echo $WidthErrorc; ?>">
													<label<?php echo $WidthErrors; ?>>Width</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $WidthError; ?>>
															<input type="text" name="Width" id="Width" value="<?php echo $Width; ?>" />
														</label>
													</div>
												</div>
												<!--</Width>-->
												<!--<Height>-->
												<div class="frm-group d-fx lbl-lf<?php echo $HeightErrorc; ?>">
													<label<?php echo $HeightErrors; ?>>Height</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $HeightError; ?>>
															<input type="text" name="Height" id="Height" value="<?php echo $Height; ?>" />
														</label>
													</div>
												</div>
												<!--</Height>-->
												<!--<Override>-->
												<div class="frm-group d-fx lbl-lf<?php echo $overrideErrorc; ?>">
													<label>Override</label>
													<div class="d-flx1">
														<label<?php echo $overrideError; ?>>
															<select name="override" id="override" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona">
																	
																	<option value="1">true</option>
																	<option value="2" <?php if ($Override == 2) { echo  ' selected="selected"'; } ?>>false</option>
																</optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Override>-->
												<!--< Ajuste altura>-->
												<div class="slider-type">
												<div class="frm-group d-fx lbl-lf<?php echo $aaErrorc; ?>">
													<label<?php echo $aaErrors; ?>>Ajuste altura</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $aaError; ?>>
															<input type="text" name="aa" value="<?php echo $AA; ?>" />
														</label>
													</div>
												</div>
												</div>
												<!--</Ajuste altura>-->
											</div>
											</div>
											<?php
											if($Type == 3){
												?>
												<div class="clmd12">
													<div class="frm-group d-fx lbl-lf">
														<label>Excluir display</label>
														<div class="d-flx1" style="margin-top: 12px;">
															<label>
																<input type="checkbox" name="displayauto" value="1"<?php if($ExcludeDisplay == 1) { echo ' checked="checked"'; } ?> />
															</label>
														</div>
													</div>
												<div>
												<?php
											}
											?>
											<div class="clmd12 show-cc">
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
										
										<?php } ?>
										<div class=" clmd12" >
											<div class="botnr-cn">
												<input type="submit" class="fa-save" value="Guardar Cambios" name="save" />
											</div>
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
	    <?php if($Type != 100){ echo ".show-cc{display:none;}"; } else { echo ".hide-cc{display:none;}"; } ?>
	    <?php if($Type <= 2){ echo ".slider-type{display:none;}"; } else { echo ".no-slider-type{display:none;}"; } ?>
	    
	</style>
    
    <!-- Javascript -->
    <script src="js/lib/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-filestyle.js"></script>
    <script src="js/jquery.autocomplete.js"></script>
    
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.8/ace.js" type="text/javascript" charset="utf-8"></script>
	<script>
		jQuery(document).ready(function($){

			$(".delad").click(function(e){
				e.preventDefault();
				$($(this).attr('href')).remove();
			});
			
			$('#newadunit').click(function(e){
				e.preventDefault();
				$('#adunits').show();
			});
			
			$(".addad").click(function(e){
				e.preventDefault();
				$('#adunits').hide();
				var idAdUnit = $(this).attr('href');
				
				$.ajax({
			        url : 'new-ad-unit.php?ida='+idAdUnit,
			        type : 'GET',
			        dataType : 'json',
			        success : function(data) {
			            $('#tbl-adunits tbody').append('<tr id="ad-' + data.id + '"><td>' + data.Name + '</td><td>' + data.Size + '</td><td>' + data.Position + '</td><td><ul class="lst-opt"><li><a href="#ad-' + data.id + '" class="fa-trash-o tt-lt delad" data-toggle="tooltip" title="" data-original-title="Quitar Ad Unit"></a></li></ul><input type="hidden" name="adunit[]" value="' + data.id + '" /></td></tr>');
			            $(".delad").click(function(e){
							e.preventDefault();
							$($(this).attr('href')).remove();
						});
			        },
			        error : function() {
			            console.log('error');
			        }
			    });
			});
			
		});
		<?php if($Type == 100) { ?>
		var editor = ace.edit("codigo");
	    editor.setTheme("ace/theme/monokai");
	    editor.getSession().setMode("ace/mode/javascript");
	
	    editor.getSession().on('change', function(e) {
	        $('#appbundle_zonas_codigo').val(editor.getValue())
	    });
	    editor.setValue($('#appbundle_zonas_codigo').val())
	    <?php } ?>
	</script>
</body>
</html><?php
					}else{
						header('Location: pages.php?idpage=' . $siteId);
						exit(0);
					}
				}else{
					header('Location: pages.php?idpage=' . $siteId);
					exit(0);
				}
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