<?php	
	@session_start();
	// Guardamos cualquier error //
	ini_set('display_errors', 0);
	//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	define('CONST',1);
	require('../config.php');
	require('../constantes.php');
	require('../db.php');
	require('../common.lib.php');
	$db = new SQL($dbhost, $dbname, $dbuser, $dbpass);
	
	
	if(@$_SESSION['Admin'] == 1 && $_SESSION['Type'] == 3){
		$AccM = 0;
		
		$userError = '';
		$userErrorc = '';
		$accmError = '';
		$accmErrorc = '';
		$accmErrors = '';
		
		//print_r($_POST);		
		if(isset($_GET['iduser'])){
			
			
			
			$idUser = intval($_GET['iduser']);
			$sql = "SELECT user FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
			$user = $db->getOne($sql);
			
			
			if(isset($_POST['save'])){
				$sigue = true;
				
				$sql = "SELECT AccM FROM " . USERS . " WHERE id = '$idUser' LIMIT 1";
				if($db->getOne($sql) == 15){
					if(intval($_POST['accm']) > 0){
						$AccM = intval($_POST['accm']);
					}else{
						$accmError = ' data-error="Debe completar el Account Manager."';
						$accmErrorc = ' frm-rrr';
						$accmErrors = ' style="margin-bottom:20px;"';
						$sigue = false;
					}
					
					if($_POST['user'] != ''){
						$user = my_clean($_POST['user']);
						$sql = "SELECT COUNT(*) FROM  " . USERS . " WHERE user LIKE '$user' AND id != '$idUser' AND AccM != 9999 ";
						if($db->getOne($sql) > 0){
							$userError = ' data-error="Usuario ya registrado."';
							$userErrorc = ' frm-rrr';
							$sigue = false;
						}
					}else{
						$userError = ' data-error="Debe completar el Usuario."';
						$userErrorc = ' frm-rrr';
						$sigue = false;
					}
					
					if($sigue){
						$Date = date('Y-m-d');
						$Time = time();
											
						//$sql = "UPDATE " . USERS . " SET AccM = '$AccM' WHERE id = '$idUser' LIMIT 1";
						//$db->query($sql);
						
						$sql = "SELECT id FROM " . APROVE . " WHERE idUser = '$idUser' LIMIT 1";
						$idAprove = $db->getOne($sql);
						if($idAprove > 0){
							$sql = "UPDATE " . APROVE . " SET AccM = '$AccM', User = '$user', Sites = '' WHERE id = '$idAprove' LIMIT 1";
							$db->query($sql);
						}else{
							$sql = "INSERT INTO " . APROVE . " (idUser, AccM, User, Sites) VALUES ('$idUser', '$AccM', '$user', '')";

							$db->query($sql);
							$idAprove = mysqli_insert_id($db->link);
						}	
						
						header('Location: aprove-sites.php?ida=' . $idAprove);
						exit(0);
					}
				}else{
					header('Location: confirm.php');
					exit(0);
				}
			}
		}else{
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
								<div class="titl">Aprobar Usuario</div>
							</div>
							<div class="bx-bd">
								<div class="bx-pd">
									<div id="formadd">
									<form action="" method="post" class="frm-adrsit">
										<div class="bx-hd dfl b-fx">
											<div class="titl">Asignar Account Manager y Usuario</div>
										</div>
										<div class="clsd-fx">
											<div class="clmd06">
												<!--<Tipo>-->
												<div class="frm-group d-fx lbl-lf<?php echo $accmErrorc; ?>">
													<label>Account Manager</label>
													<div class="d-flx1">
														<label<?php echo $accmError; ?>>
															<select name="accm" id="accm" data-dropdown-options='{"customClass":"slct-hd", "label":"Seleccionar"}'>
																<optgroup label="Selecciona el Tipo"><?php
																
																$sql = "SELECT * FROM " . ACC_MANAGERS . " WHERE Deleted = 0  AND id != '15' AND id != 1  ORDER BY Name ASC";
												
																$query = $db->query($sql);
																if($db->num_rows($query) > 0){
																	while($User = $db->fetch_array($query)){
																		?><option value="<?php echo $User['id']; ?>"<?php if($User['id'] == 10){ echo ' selected="selected"'; } ?>><?php echo $User['Name']; ?></option><?php
																	}
																}
																
																?></optgroup>
															</select>
														</label>
													</div>
												</div>
												<!--</Tipo>-->
											</div>
											<div class="clmd06">
												<div class="frm-group d-fx lbl-lf<?php echo $userErrorc; ?>">
													<label>Usuario</label>
													<div class="d-flx1">
														<label class="lbl-icon ncn-lf"<?php echo $userError; ?>>
															<input type="text" name="user" value="<?php echo $user; ?>" />
														</label>
													</div>
												</div>
											</div>
										</div>
										<div class="botnr-cn">
											<input type="submit" class="fa-save" value="Asignar y Verificar Sitio(s)" name="save" /> 	
										</div>
									</form>
									</div>
								</div>
							</div>
						
							<div class="bx-hd dfl b-fx">
								<div class="titl">Resumen de Sitios</div>
							</div>
							<div class="bx-bd">
								
							<!--<table>-->
							<div class="tbl-cn">
								<table id="tbl-estats">
									<thead>
										<tr>
											<th>Dominio</th>
											<th>Visitas Declaradas</th>
											<th>Visitas Detectadas</th>
											<th>Ads.txt</th>
										</tr>
									</thead> 
									
									<tbody><?php
									
									$sql = "SELECT * FROM " . SITES . " WHERE idUser = '$idUser' ";
									$query = $db->query($sql);
									if($db->num_rows($query) > 0){
										while($Site = $db->fetch_array($query)){
											?><tr>
												<td data-title="Dominio"><?php echo $Site['siteurl']; ?></td>
												<td data-title="VisitasDeclaradas"><?php echo $Vis[$Site['views']]; ?></td>
												<td data-title="VisitasDetectadas"><?php echo totalViews($Site['id']); ?></td>
												<td data-title="Adstxt"><?php echo $AdstxtState[$Site['adstxt']]; ?></td>
											</tr><?php
										}
									}
									?>
									</tbody>
								</table>
							</div>
							<!--</table>-->
								
								
								
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
		<?php if($AccM > 0){ ?>
		$("#accm").val(<?php echo $AccM; ?>).trigger("change");
		<?php } ?>
	});
	
	</script>
</body>
</html>
</html><?php
	//echo date('H:i:s', time()); 
	}else{
		header('Location: index.php');
		exit(0);
	}
?>